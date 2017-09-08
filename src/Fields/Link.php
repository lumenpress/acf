<?php

namespace Lumenpress\ACF\Fields;

class Link extends Field
{
    protected $defaults = [
        'type' => 'link',
        'return_format' => 'url',
    ];

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        return is_array($this->metaValue) ? new LinkObject($this->metaValue, 'url') : null;
    }

    /**
     * Mutator for MetaValue attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($value)
    {
        if (is_string($value)) {
            $value = [
                'url' => $value, 
                'title' => '', 
                'target' => ''
            ];
        }
        parent::setMetaValueAttribute($value);
    }
}

class LinkObject
{
    protected $primaryKey;

    protected $attributes = [];

    public function __construct(array $attributes, $primaryKey = null)
    {
        $this->attributes = $attributes;
        $this->primaryKey = $primaryKey;
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : '';
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __toString()
    {
        return $this->primaryKey ? $this->{$this->primaryKey} : '';
    }
}
