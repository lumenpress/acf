<?php

namespace LumenPress\ACF\Fields;

class Group extends Field
{
    /**
     * @var array
     */
    protected $items;

    /**
     * [$with description].
     * @var array
     */
    protected $with = ['fields'];

    /**
     * [$defaults description].
     * @var [type]
     */
    protected $defaults = [
        'type' => 'group',
        'layout' => 'block',
        // 'sub_fields' => []
    ];

    /**
     * Accessor for Value attribute.
     *
     * @return mixed
     */
    public function getMetaValueAttribute($value)
    {
        // group field meta value is empty
        if (is_null($this->items)) {
            $this->items = [];
            foreach ($this->fields as $field) {
                $metaKey = "{$this->meta_key}_{$field->name}";
                if (is_null($metaValue = $this->relatedParent->acfdata->$metaKey)) {
                    continue;
                }
                $field = clone $field;
                $field->setRelatedParent($this->relatedParent);
                $field->meta_key = $metaKey;
                $field->meta_value = $metaValue;
                $this->items[$field->name] = $field;
            }
        }

        return array_map(function ($item) {
            return $item->value;
        }, $this->items);
    }

    public function setMetaValueAttribute($values)
    {
        if (! is_array($values)) {
            return $this;
        }
        foreach ($this->fields as $field) {
            if (! isset($values[$field->name])) {
                continue;
            }
            $field = clone $field;
            $field->setRelatedParent($this->relatedParent);
            $field->meta_key = "{$this->meta_key}_{$field->name}";
            $field->meta_value = $values[$field->name];
            $this->items[$field->name] = $field;
        }
        parent::setMetaValueAttribute('');
    }

    public function updateValue()
    {
        foreach ($this->items as $field) {
            $field->updateValue();
        }

        return parent::updateValue();
    }
}
