<?php 

namespace Lumenpress\Acf\Fields;

use Lumenpress\Acf\Collections\LayoutCollection;

class FlexibleContent extends Field
{
    protected $layouts;

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

    public function layouts(callable $callable)
    {
        $callable($this->layouts = $this->getLayoutsAttribute(null));
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
        if (!$this->layouts) {
            $layouts = $this->getContentAttribute('layouts', []);
            
            foreach ($this->fields as $field) {
                $layouts[$field->getContentAttribute('parent_layout')]['fields'][] = $field;
            }

            foreach ($layouts as $key => $layout) {
                $layouts[$key] = (new FlexibleLayout($layout))->setRelatedParent($this);
            }

            $this->layouts = LayoutCollection::create($layouts, FlexibleLayout::class);
            $this->layouts->setRelatedParent($this);
        }

        return $this->layouts;
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (!is_array(parent::getMetaValueAttribute($value))) {
            return [];
        }
        $values = [];
        foreach ($this->metaValue as $index => $name) {
            foreach ($this->layouts as $layout) {
                $values[$index]['acf_layout'] = $name;
                if ($layout->name == $name) {
                    foreach ($layout->fields as $field) {
                        $field = clone $field;
                        $field->setRelatedParent($this);
                        $field->meta_key = "{$this->meta_key}_{$index}_{$field->name}";
                        $field->meta_value = $this->relatedParent->meta->{"{$this->meta_key}_{$index}_{$field->name}"};
                        $values[$index][$field->name] = $field;
                    }
                }
            }
        }
        return $values;
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
