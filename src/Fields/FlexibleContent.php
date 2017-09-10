<?php

namespace LumenPress\ACF\Fields;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Jsonable;
use LumenPress\ACF\Collections\FieldCollection;
use Illuminate\Database\Eloquent\JsonEncodingException;
use LumenPress\Nimble\Collections\Collection as AbstractCollection;

class FlexibleContent extends Field
{
    protected $_layouts;

    protected $values;

    protected $hidden = ['fields'];

    protected $defaults = [
        // 'key' => 'field_5979ac6c766e3',
        // 'label' => 'Flexible Content',
        // 'name' => 'flexible_content',
        'type' => 'flexible_content',
        'layouts' => [
            // '5979ac7105e3b' => [
            //     'key' => '5979ac7105e3b',
            //     'name' => '',
            //     'label' => '',
            //     'display' => 'block',
            //     'sub_fields' => [],
            //     'min' => '',
            //     'max' => ''
            // ]
        ],
        'button_label' => 'Add Row',
        'min' => '',
        'max' => '',
    ];

    public function layouts(callable $callable)
    {
        $callable($this->_layouts = $this->getLayoutsAttribute(null));
        $this->setLayoutsToContent();

        return $this;
    }

    /**
     * Accessor for layouts attribute.
     *
     * @return returnType
     */
    public function getLayoutsAttribute($value)
    {
        if (! $this->_layouts) {
            $layouts = $this->getContentAttribute('layouts', []);

            foreach ($this->fields as $field) {
                $layouts[$field->getContentAttribute('parent_layout')]['fields'][] = $field;
            }

            foreach ($layouts as $key => $layout) {
                $layouts[$key] = (new FlexibleLayout($layout))->setRelatedParent($this);
            }

            $this->_layouts = new LayoutCollection($layouts);
            $this->_layouts->setRelatedParent($this);
        }

        return $this->_layouts;
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (! is_array(parent::getMetaValueAttribute($value))) {
            return [];
        }

        if (is_null($this->values)) {
            foreach ($this->metaValue as $index => $name) {
                foreach ($this->layouts as $layout) {
                    $this->values[$index]['_layout'] = $name;
                    if ($layout->name == $name) {
                        foreach ($layout->fields as $field) {
                            $metaKey = "{$this->meta_key}_{$index}_{$field->name}";
                            // d($this->relatedParent);
                            if (is_null($metaValue = $this->relatedParent->meta->$metaKey)) {
                                continue;
                            }
                            $field = clone $field;
                            $field->setRelatedParent($this->relatedParent);
                            $field->meta_key = $metaKey;
                            $field->meta_value = $metaValue;
                            $this->values[$index][$field->name] = $field;
                        }
                    }
                }
            }
        }

        return (new Collection($this->values))->map(function($row) {
            $item = [];
            foreach ($row as $key => $column) {
                $item[$key] = $column instanceof Field ? $column->value : $column;
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
            return $this;
        }
        // init
        if (! is_array($values[0])) {
            return parent::setMetaValueAttribute($values);
        }
        foreach ($values as $index => $item) {
            if (! is_numeric($index)) {
                throw new \Exception("$index invalid", 1);
            }
            if (! isset($item['_layout'])) {
                throw new \Exception('_layout does not exists.', 1);
            }
            foreach ($this->layouts as $layout) {
                if ($layout->name == $item['_layout']) {
                    $this->metaValue[$index] = $this->values[$index]['_layout'] = $item['_layout'];
                    foreach ($layout->fields as $field) {
                        if (! isset($item[$field->name])) {
                            continue;
                        }
                        $field = clone $field;
                        $field->setRelatedParent($this->relatedParent);
                        $field->meta_key = "{$this->meta_key}_{$index}_{$field->name}";
                        $field->meta_value = $item[$field->name];
                        $this->values[$index][$field->name] = $field;
                    }
                }
            }
        }
        // parent::setMetaValueAttribute(count($values));
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($this->layouts as $key => $layout) {
            $attributes['layouts'][$key] = $layout->toArray();
        }

        return $attributes;
    }

    public function save(array $options = [])
    {
        if (! parent::save($options)) {
            return false;
        }

        $this->layouts->save();
        $this->setLayoutsToContent();

        return true;
    }

    public function updateValue()
    {
        foreach ($this->values as $item) {
            foreach ($item as $field) {
                if ($field instanceof Field) {
                    $field->updateValue();
                }
            }
        }

        return parent::updateValue();
    }

    protected function setLayoutsToContent()
    {
        $values = [];

        foreach ($this->layouts as $key => $layout) {
            $values[$key] = $layout->getAttributes();
        }

        $this->setContentAttribute('layouts', $values);
        unset($values);
    }
}

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
            $this->fields = new FieldCollection($attributes['fields']);
            unset($attributes['fields']);
        } else {
            $this->fields = new FieldCollection;
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

class LayoutCollection extends AbstractCollection
{
    public function layout($name)
    {
        foreach ($this->items as $index => $item) {
            if ($item->name == $name) {
                return $item;
            }
        }
        $item = new FlexibleLayout;
        $item->key = uniqid();
        $item->name = $name;
        $item->label = $name;
        $item->setRelatedParent($this->relatedParent);

        return $this->items[$item->key] = $item;
    }

    public function save()
    {
        foreach ($this->items as $item) {
            $item->save();
        }
    }
}
