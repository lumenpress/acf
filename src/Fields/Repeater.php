<?php

namespace LumenPress\ACF\Fields;

use Illuminate\Support\Collection;

class Repeater extends Field
{
    /**
     * [$items description].
     * @var [type]
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
        'type' => 'repeater',
        'collapsed' => '',
        'min' => 0,
        'max' => 0,
        'layout' => 'row',
        'button_label' => '',
        // 'sub_fields' => []
    ];

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        // init
        if (is_null(parent::getMetaValueAttribute($value))) {
            return [];
        }

        if (is_null($this->items)) {
            $this->items = [];
            foreach ($this->fields as $field) {
                for ($i = 0; $i < $this->metaValue ?: 0; $i++) {
                    $metaKey = "{$this->meta_key}_{$i}_{$field->name}";
                    if (is_null($metaValue = $this->relatedParent->meta->$metaKey)) {
                        continue;
                    }
                    $field = clone $field;
                    $field->setRelatedParent($this->relatedParent);
                    $field->meta_key = $metaKey;
                    $field->meta_value = $metaValue;
                    $this->items[$i][$field->name] = $field;
                }
            }
            ksort($this->items);
        }

        return (new Collection($this->items))->map(function ($row) {
            $item = [];

            foreach ($row as $key => $column) {
                $item[$key] = $column->value;
            }

            return $item;
        });
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($values)
    {
        if (! is_array($values)) {
            return parent::setMetaValueAttribute($values);
        }

        foreach ($values as $index => $item) {
            if (! is_numeric($index)) {
                throw new \Exception("$index invalid", 1);
            }
            foreach ($this->fields as $field) {
                if (! isset($item[$field->name])) {
                    continue;
                }
                $field = clone $field;
                $field->setRelatedParent($this->relatedParent);
                $field->meta_key = "{$this->meta_key}_{$index}_{$field->name}";
                $field->meta_value = $item[$field->name];
                $this->items[$index][$field->name] = $field;
            }
        }

        ksort($this->items);

        parent::setMetaValueAttribute(count($values));
    }

    public function updateValue()
    {
        foreach ($this->items as $item) {
            foreach ($item as $field) {
                $field->updateValue();
            }
        }

        return parent::updateValue();
    }

    public function deleteValue()
    {
        foreach ($this->items as $item) {
            foreach ($item as $field) {
                $field->deleteValue();
            }
        }

        return parent::deleteValue();
    }
}
