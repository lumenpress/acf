<?php

namespace LumenPress\ACF\Concerns;

use Illuminate\Support\Str;
use LumenPress\ACF\Models\FieldGroup;
use LumenPress\Nimble\Relations\HasMeta;
use LumenPress\ACF\Relations\HasACF as ACF;

trait HasACF
{
    /**
     * HasAdvancedCustomFields has many ACF.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function acf($key = null)
    {
        $relation = new ACF($this);
        if ($key) {
            $relation->whereKeyIs($key);
        }

        return $relation;
    }

    public function acfdata()
    {
        return new HasMeta($this);
    }

    /**
     * [getAcfFieldObjects description].
     * @return [type] [description]
     */
    public function getAcfFieldObjects()
    {
        $fields = [];
        FieldGroup::where('post_status', 'publish')->get()->filter(function ($item) {
            return $item->locationRuleMatch($this);
        })->each(function ($item) use (&$fields) {
            foreach ($item->fields as $field) {
                $field->setRelatedParent($this);
                $field->meta_key = $field->name;
                $field->meta_value = $this->acfdata->{$field->name};
                $fields[$field->name] = $field;
            }
        });

        return $fields;
    }

    /**
     * [isLocation description].
     * @param  [type]  $param    [description]
     * @param  [type]  $operator [description]
     * @param  [type]  $value    [description]
     * @return bool           [description]
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
        //
        $method = 'get'.Str::studly($param).'LocationRuleValue';
        if (method_exists($this, $method)) {
            $paramValue = $this->{$method}();
            eval("\$bool = '{$paramValue}' $operator '$value';");
        } elseif (isset($this->$param)) {
            $paramValue = $this->$param;
            eval("\$bool = '{$this->$param}' $operator '$value';");
        }

        return $bool;
    }
}
