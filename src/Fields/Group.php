<?php 

namespace Lumenpress\Acf\Fields;

class Group extends Field implements \IteratorAggregate 
{
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
        return new \ArrayIterator($this->value);
    }

    public function count()
    {
        return count($this->value);
    }

    public function getAttribute($key)
    {
        if (isset($this->rawValue[$key])) {
            return $this->rawValue[$key];
        }
        return parent::getAttribute($key);
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        return is_array($this->rawValue) ? $this->rawValue : [];
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (!isset($value[0])) {
            $value = [$value];
        }
        if (!$this->fullName) {
            $this->fullName = $this->name;
        }
        $object = [];
        foreach ($this->fields as $field) {
            $field->fullName = $this->fullName . '_' . $field->name;
            $field->value = $this->object->meta->{$field->fullName};
            $object[$field->name] = clone $field;
        }
        $this->rawValue = $object;
        /*
        if (!is_array($value)) {
            $values = [];
            for ($i=0; $i < $value; $i++) { 
                $object = [];
                foreach ($this->fields as $field) {
                    $field->fullName = $this->fullName . '_' . $i . '_' . $field->name;
                    $field->value = $this->object->meta->{$field->fullName};
                    $object[$field->name] = clone $field;
                }
                $values[] = $object;
            }
            $this->rawValue = $values;
            return;
        }
        foreach ($value as $index => $object) {
            foreach ($object as $key => $val) {
                $field = clone $this->fields->filter(function($item) use ($key) {
                    return $item->name == $key;
                })->first();
                $field->value = $val;
                $field->fullName = "{$this->fullName}_{$index}_{$key}";
                $value[$index][$key] = $field;
            }
        }
        $this->rawValue = $value;
        */
    }
}
