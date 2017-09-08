<?php

namespace Lumenpress\ACF\Fields;

use Carbon\Carbon;

class DatePicker extends DateTimePicker
{
    protected $defaults = [
        // 'key' => 'field_5979abf7766db',
        // 'label' => 'Date Picker',
        // 'name' => 'date_picker',
        'type' => 'date_picker',
        'display_format' => 'd/m/Y',
        'return_format' => 'd/m/Y',
        'first_day' => 1,
    ];
}
