<?php 

namespace Lumenpress\Acf\Fields;

class TimePicker extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ac18766dd',
        // 'label' => 'Time Picker',
        // 'name' => 'time_picker',
        'type' => 'time_picker',
        'display_format' => 'g:i a',
        'return_format' => 'g:i a'
    ];
}