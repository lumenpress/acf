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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $layouts = $this->getContentAttribute('layouts', []);
        foreach ($this->fields as $field) {
            $layouts[$field->getContentAttribute('parent_layout')]['fields'][] = $field;
        }
        foreach ($layouts as $layout) {
            $layouts[] = new FlexibleLayout($layout);
        }
        $this->layouts = LayoutCollection::create($layouts, FlexibleLayout::class);
        $this->layouts->setRelatedParent($this);
    }

    public function layouts(callable $callable)
    {
        $callable($this->layouts);
        $this->setLayoutsAttribute($this->layouts);
        return $this;
    }

    /**
     * Mutator for layouts attribute.
     *
     * @return void
     */
    public function setLayoutsAttribute($layouts)
    {
        $values = [];
        foreach ($layouts as $layout) {
            $values[] = $layout->getAttributes();
        }
        $this->setContentAttribute('layouts', $values);
    }

    public function save(array $options = [])
    {
        if (!parent::save($options)) {
            return false;
        }

        $this->layouts->save();
        $this->setLayoutsAttribute($this->layouts);

        return true;
    }

}
