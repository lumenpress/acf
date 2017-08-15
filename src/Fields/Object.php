<?php 

namespace Lumenpress\Acf\Fields;

class Object extends Repeater
{
    /**
     * [$with description]
     * @var array
     */
    protected $with = ['fields'];

    /**
     * [$defaults description]
     * @var [type]
     */
    protected $defaults = [
        // 'key' => 'field_5979ac4d766e1',
        // 'label' => 'Repeater',
        // 'name' => 'repeater',
        'type' => 'repeater',
        'collapsed' => '',
        'min' => 1,
        'max' => 1,
        'layout' => 'block',
        'button_label' => '',
        // 'sub_fields' => []
    ];
}
