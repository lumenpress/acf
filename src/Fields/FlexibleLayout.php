<?php 

namespace Lumenpress\ACF\Fields;

use Lumenpress\ACF\Collections\FieldCollection;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\JsonEncodingException;

class FlexibleLayout implements Jsonable
{
    public $fields;

    protected $relatedParent;

    protected $attributes = [
        'display' => 'block',
        'min' => '',
        'max' => '',
    ];

    public function __construct(array $attributes = [])
    {
        if (array_key_exists('fields', $attributes)) {
            $this->fields = FieldCollection::create($attributes['fields'], Field::class);
            unset($attributes['fields']);
        } else {
            $this->fields = FieldCollection::create([], Field::class);
        }

        $this->fields->setRelatedParent($this);

        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }
    public function __get($key)
    {
        if ($key === 'id') {
            return $this->getIdAttribute(null);
        }
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __call($key, $values)
    {
        $this->attributes[$key] = array_shift($values);
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function toArray()
    {
        return array_merge($this->attributes, ['fields' => $this->fields]);
    }

    public function toJson($options = 0)
    {
        $json = json_encode($this->toArray(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    public function getIdAttribute($value)
    {
        return $this->relatedParent->id;
    }

    public function setRelatedParent(&$relatedParent)
    {
        $this->relatedParent = $relatedParent;
        return $this;
    }

    public function fields($callable = null)
    {
        if (is_callable($callable)) {
            $callable($this->fields);
        }
    }

    public function save()
    {
        return $this->fields->save();
    }

}
