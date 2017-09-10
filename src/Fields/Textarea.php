<?php

namespace LumenPress\ACF\Fields;

class Textarea extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979a86e1de29',
        // 'label' => 'textarea',
        // 'name' => 'textarea',
        'type' => 'textarea',
        'default_value' => '',
        'placeholder' => '',
        'maxlength' => '',
        'rows' => '',
        'new_lines' => 'wpautop',
    ];
}
