<?php 

namespace Lumenpress\Acf\Fields;

class Repeater extends Field implements \IteratorAggregate 
{
    /**
     * [$values description]
     * @var [type]
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
        'type' => 'repeater',
        'collapsed' => '',
        'min' => 0,
        'max' => 0,
        'layout' => 'table',
        'button_label' => '',
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
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (!empty($this->values)) {
            return $this->values;
        }
        if (is_null(parent::getMetaValueAttribute($value))) {
            return [];
        }
        foreach ($this->fields as $field) {
            $field->setRelatedParent($this);
            for ($i=0; $i < $this->metaValue?:0; $i++) {
                $field = clone $field;
                $field->meta_key = "{$this->meta_key}_{$i}_{$field->name}";
                $field->meta_value = $this->relatedParent->meta->{"{$this->meta_key}_{$i}_{$field->name}"};
                $this->values[$i][$field->name] = $field;
            }
        }
        return $this->values;
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($values)
    {
        if (!is_array($values)) {
            return $this;
        }
        foreach ($values as $index => $item) {
            if (!is_numeric($index)) {
                throw new \Exception("$index invalid", 1);
            }
            foreach ($this->fields as $field) {
                if (!isset($item[$field->name])) {
                    continue;
                }
                $field->setRelatedParent($this);
                $field = clone $field;
                $field->meta_key = "{$this->meta_key}_{$index}_{$field->name}";
                $field->meta_value = $item[$field->name];
                $this->values[$index][$field->name] = $field;
            }
        }
        parent::setMetaValueAttribute(count($values));
    }

}
