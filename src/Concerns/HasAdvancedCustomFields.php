<?php 

namespace Lumenpress\Models\Acf\Concerns;

use Lumenpress\Models\Acf\FieldGroup;
use Lumenpress\Models\Post;

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
            return $item->showIn($this);
        })->each(function($item) use (&$fields) {
            foreach ($item->fields as $field) {
                $field->object = $this;
                // d(get_class($field));
                if ($field instanceof \Lumenpress\Models\Acf\Fields\Repeater) {
                    # code...
                } else {
                    $field->value = $this->meta->{$field->name};
                }
                // $field->value = $this->meta->{"_".$field->fullName};
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

    protected function getAcfKey()
    {
        return $this->table == 'options' ? 'option_key' : 'meta_key';
    }

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

    protected function getAcfRelated()
    {
        switch ($this->table) {
            case 'posts':
                return \Lumenpress\Models\Acf\PostAcf::class;
            case 'terms':
                return \Lumenpress\Models\Acf\TermAcf::class;
            case 'users':
                return \Lumenpress\Models\Acf\UserAcf::class;
            case 'comments':
                return \Lumenpress\Models\Acf\CommentAcf::class;
            default:
                return \Lumenpress\Models\Acf\OptionAcf::class;
        }
        return PostAcf::class;
    }
}
