<?php 

namespace Lumenpress\Acf\Fields;

use Lumenpress\Acf\Collections\FieldCollection;

class FlexibleLayout
{
    protected $relatedParent;

    protected $attributes = [
        'display' => 'block',
        'min' => '',
        'max' => '',
        'fields' => [],
    ];

    public function __construct(array $attributes = [])
    {
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

    public function getIdAttribute($value)
    {
        return $this->relatedParent->id;
    }

    public function setRelatedParent(&$relatedParent)
    {
        $this->relatedParent = $relatedParent;
        return $this;
    }

    public function fields(callable $callable)
    {
        $this->fields = FieldCollection::create($this->fields, Field::class);
        $this->fields->setRelatedParent($this);
        $callable($this->fields);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function save()
    {
        return $this->fields->save();
    }
}
