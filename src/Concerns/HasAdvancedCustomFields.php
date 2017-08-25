<?php 

namespace Lumenpress\Acf\Concerns;

use Lumenpress\Acf\Models\FieldGroup;
use Lumenpress\ORM\Models\Post;

trait HasAdvancedCustomFields
{
    /**
     * HasAdvancedCustomFields has many Acf.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function acf($key = null)
    {
        $relation = $this->hasAcf();
        if ($key) {
            $relation->where($this->getAcfKey(), $key);
        }
        return $relation;
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasAcf($related = null, $foreignKey = null, $localKey = null)
    {
        return $this->hasMany($this->getAcfRelated(), $this->getAcfForeignKey(), $localKey);
    }

    /**
     * [getAcfFieldObjects description]
     * @return [type] [description]
     */
    public function getAcfFieldObjects()
    {
        $fields = [];
        FieldGroup::where('post_status', 'publish')->get()->filter(function($item) {
            return $item->locationRuleMatch($this);
        })->each(function($item) use (&$fields) {
            foreach ($item->fields as $field) {
                $field->setRelatedParent($this);
                $field->meta_key = $field->name;
                $field->meta_value = $this->meta->{$field->name};
                $fields[$field->name] = $field;
            }
        });
        return $fields;
    }

    /**
     * [isLocation description]
     * @param  [type]  $param    [description]
     * @param  [type]  $operator [description]
     * @param  [type]  $value    [description]
     * @return boolean           [description]
     */
    public function locationRuleMatch($param, $operator, $value)
    {
        if ($param == 'page_template') {
            $param = 'template';
        } elseif ($param == 'post_template') {
            $param = 'template';
        }

        $bool = false;
        // getPostTypeLocationRuleValue
        $method = 'get'.studly_case($param).'LocationRuleValue';
        if (method_exists($this, $method)) {
            $paramValue = $this->{$method}();
            eval("\$bool = '{$paramValue}' $operator '$value';");
        } elseif (isset($this->$param)) {
            $paramValue = $this->$param;
            eval("\$bool = '{$this->$param}' $operator '$value';");
        }
        return $bool;
    }

    /**
     * [getAcfKey description]
     * @return [type] [description]
     */
    protected function getAcfKey()
    {
        return $this->table == 'options' ? 'option_key' : 'meta_key';
    }

    /**
     * [getAcfForeignKey description]
     * @return [type] [description]
     */
    protected function getAcfForeignKey()
    {
        switch ($this->table) {
            case 'posts':
                return 'post_id';
            case 'terms':
                return 'term_id';
            case 'users':
                return 'user_id';
            case 'comments':
                return 'comment_id';
            default:
                # code...
                break;
        }
    }

    /**
     * [getAcfRelated description]
     * @return [type] [description]
     */
    protected function getAcfRelated()
    {
        switch ($this->table) {
            case 'posts':
                return \Lumenpress\Acf\Models\PostField::class;
            case 'terms':
                return \Lumenpress\Acf\Models\TermField::class;
            case 'users':
                return \Lumenpress\Acf\Models\UserField::class;
            case 'comments':
                return \Lumenpress\Acf\Models\CommentField::class;
            default:
                return \Lumenpress\Acf\Models\OptionField::class;
        }
    }
}
