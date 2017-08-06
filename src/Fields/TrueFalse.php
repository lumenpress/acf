<?php 

namespace Lumenpress\Models\Acf\Fields;

class TrueFalse extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ab21ae3ea',
        // 'label' => 'Boolean',
        // 'name' => 'boolean',
        'type' => 'true_false',
        'message' => '',
        'default_value' => 0,
        'ui' => 0,
        'ui_on_text' => '',
        'ui_off_text' => ''
    ];
}