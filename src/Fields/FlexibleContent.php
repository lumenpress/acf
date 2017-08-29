<?php 

namespace Lumenpress\Acf\Fields;

use Lumenpress\Acf\Collections\LayoutCollection;

class FlexibleContent extends Field implements \IteratorAggregate 
{
    protected $_layouts;

    protected $values = [];

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
        'max' => ''
    ];

    public function getAttribute($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return parent::getAttribute($key);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    public function count()
    {
        return count($this->values);
    }

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
        if (!$this->_layouts) {
            $layouts = $this->getContentAttribute('layouts', []);

            foreach ($this->fields as $field) {
                $layouts[$field->getContentAttribute('parent_layout')]['fields'][] = $field;
            }

            foreach ($layouts as $key => $layout) {
                $layouts[$key] = (new FlexibleLayout($layout))->setRelatedParent($this);
            }

            $this->_layouts = LayoutCollection::create($layouts, FlexibleLayout::class);
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
        if (!empty($this->values)) {
            return $this->values;
        }
        if (!is_array(parent::getMetaValueAttribute($value))) {
            return [];
        }
        foreach ($this->metaValue as $index => $name) {
            foreach ($this->layouts as $layout) {
                $this->values[$index]['_layout'] = $name;
                if ($layout->name == $name) {
                    foreach ($layout->fields as $field) {
                        $field = clone $field;
                        $field->setRelatedParent($this);
                        $field->meta_key = "{$this->meta_key}_{$index}_{$field->name}";
                        $field->meta_value = $this->relatedParent->meta->{"{$this->meta_key}_{$index}_{$field->name}"};
                        $this->values[$index][$field->name] = $field;
                    }
                }
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
            if (!isset($item['_layout'])) {
                throw new \Exception("_layout does not exists.", 1);
            }
            foreach ($this->layouts as $layout) {
                if ($layout->name == $item['_layout']) {
                    $this->metaValue[$index] = $this->values[$index]['_layout'] = $item['_layout'];
                    foreach ($layout->fields as $field) {
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
        if (!parent::save($options)) {
            return false;
        }

        $this->layouts->save();
        $this->setLayoutsToContent();

        return true;
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
