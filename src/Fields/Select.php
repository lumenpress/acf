<?php

namespace LumenPress\ACF\Fields;

class Select extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979aaf6ae3e7',
        // 'label' => 'Select',
        // 'name' => 'select',
        'type' => 'select',
        'choices' => [],
        'default_value' => [],
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'ajax' => 0,
        'return_format' => 'value',
        'placeholder' => '',
    ];

    /**
     * Mutator for choices attribute.
     *
     * @return void
     */
    public function setChoicesAttribute($choices)
    {
        if (isset($choices[0])) {
            foreach ($choices as $key => $value) {
                unset($choices[$key]);
                $choices[$value] = $value;
            }
        }
        $this->setContentAttribute('choices', $choices);
    }
}
