<?php 

namespace Lumenpress\Models\Acf\Fields;

class Radio extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ab0dae3e9',
        // 'label' => 'Radio',
        // 'name' => 'radio',
        'type' => 'radio',
        'choices' => [],
        'allow_null' => 0,
        'other_choice' => 0,
        'save_other_choice' => 0,
        'default_value' => '',
        'layout' => 'vertical',
        'return_format' => 'value'
    ];
}