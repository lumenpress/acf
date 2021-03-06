<?php

namespace LumenPress\ACF\Fields;

class Checkbox extends Select
{
    protected $defaults = [
        // 'key' => 'field_5979ab03ae3e8',
        // 'label' => 'Checkbox',
        // 'name' => 'checkbox',
        'type' => 'checkbox',
        'choices' => [],
        'allow_custom' => 0,
        'save_custom' => 0,
        'default_value' => [],
        'layout' => 'vertical',
        'toggle' => 0,
        'return_format' => 'value',
    ];
}
