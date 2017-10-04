<?php

namespace LumenPress\ACF\Concerns;

use Illuminate\Support\Str;
use LumenPress\Nimble\Models\Meta;
use LumenPress\ACF\Models\MetaField;
use LumenPress\ACF\Models\FieldGroup;
use LumenPress\ACF\Models\OptionField;
use LumenPress\ACF\Relations\HasMetaFields;
use LumenPress\ACF\Relations\HasOptionFields;

trait HasFields
{
    public function acf($key = null)
    {
        if ($this->table == 'options') {
            $related = $this->newRelatedInstance(OptionField::class);
            $relation = new HasOptionFields($related->newQuery(), $this);
        } else {
            $related = $this->newRelatedInstance(MetaField::class);
            $relation = new HasMetaFields($related->newQuery(), $this);
        }

        unset($related);

        if ($key) {
            $relation->whereKeyIs($key);
        }

        return $relation;
    }

    public function acfdata()
    {
        if ($this->table == 'options') {
            return new HasOptionFields($this->newQuery(), $this);
        }

        return new HasMetaFields($this->newRelatedInstance(Meta::class)->newQuery(), $this);
    }

    public function newRelatedInstance($class)
    {
        return tap(new $class, function ($instance) {
            if ($instance instanceof Meta) {
                $instance->setTableThroughParentTable($this->getTable());
            }

            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->getConnectionName());
            }
        });
    }

    public function getAcfFieldObjects()
    {
        $fields = [];

        FieldGroup::where('post_status', 'publish')->get()->filter(function ($item) {
            return $item->locationRuleMatch($this);
        })->each(function ($item) use (&$fields) {
            foreach ($item->fields as $field) {
                $field->setRelatedParent($this);
                $field->meta_key = $field->name;
                $name = $this->table === 'options' ? "options_{$field->name}" : $field->name;
                $field->meta_value = isset($this->acfdata->$name) ? $this->acfdata->$name : null;
                $fields[$field->name] = $field;
            }
        });

        return $fields;
    }

    public function locationRuleMatch($param, $operator, $value)
    {
        if ($param == 'page_template') {
            $param = 'template';
        } elseif ($param == 'post_template') {
            $param = 'template';
        }

        $bool = false;
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
