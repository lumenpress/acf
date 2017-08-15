<?php 

namespace Lumenpress\Acf\Fields;

class DateTimePicker extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ac03766dc',
        // 'label' => 'Date Time Picker',
        // 'name' => 'date_time_picker',
        'type' => 'date_time_picker',
        'display_format' => 'd/m/Y g:i a',
        'return_format' => 'd/m/Y g:i a',
        'first_day' => 1
    ];
}
