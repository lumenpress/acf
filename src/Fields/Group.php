<?php 

namespace Lumenpress\Acf\Fields;

class Group extends Field implements \IteratorAggregate 
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * [$with description]
     * @var array
     */
    protected $with = ['fields'];

    /**
     * [$defaults description]
     * @var [type]
     */
    protected $defaults = [
        // 'key' => 'field_5979ac4d766e1',
        // 'label' => 'Repeater',
        // 'name' => 'repeater',
        'type' => 'group',
        'layout' => 'block',
        // 'sub_fields' => []
    ];

    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    public function count()
    {
        return count($this->values);
    }

    public function getAttribute($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return parent::getAttribute($key);
    }

    /**
     * Accessor for Value attribute.
     *
     * @return mixed
     */
    public function getMetaValueAttribute($value)
    {
        if (!empty($this->values)) {
            return $this->values;
        }
        if (is_null(parent::getMetaValueAttribute($value))) {
            return;
        }
        foreach ($this->fields as $field) {
            $metaKey = "{$this->meta_key}_{$field->name}";
            if (is_null($metaValue = $this->relatedParent->meta->$metaKey)) {
                continue;
            }
            $field = clone $field;
            $field->setRelatedParent($this);
            $field->meta_key = $metaKey;
            $field->meta_value = $metaValue;
            $this->values[$field->name] = $field;
        }
        unset($metaKey, $metaValue);
        return $this->values;
    }

    public function setMetaValueAttribute($values)
    {
        if (!is_array($values)) {
            return $this;
        }
        foreach ($this->fields as $field) {
            if (!isset($values[$field->name])) {
                continue;
            }
            $field = clone $field;
            $field->setRelatedParent($this);
            $field->meta_key = "{$this->meta_key}_{$field->name}";
            $field->meta_value = $values[$field->name];
            $this->values[$field->name] = $field;
        }
        parent::setMetaValueAttribute('');
    }

    public function updateValue()
    {
        if (!parent::updateValue()) {
            return false;
        }
        foreach ($this->values as $field) {
            $field->updateValue();
        }
    }

    // public function toArray()
    // {
    //     return $this->values;
    // }

}
