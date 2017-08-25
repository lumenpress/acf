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

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (is_null(parent::getMetaValueAttribute($value))) {
            return;
        }
        $values = [];
        foreach ($this->fields as $field) {
            $field->setRelatedParent($this);
            $field = clone $field;
            $field->meta_key = "{$this->meta_key}_{$field->name}";
            $field->meta_value = $this->relatedParent->meta->{"{$this->meta_key}_{$field->name}"};
            $values[$field->name] = $field;
        }
        return $values;
    }

}
