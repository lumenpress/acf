<?php 

namespace Lumenpress\Acf\Fields;

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
        'placeholder' => ''
    ];
}