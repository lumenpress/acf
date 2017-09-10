<?php

namespace LumenPress\ACF\Fields;

class TimePicker extends DateTimePicker
{
    protected $defaults = [
        'type' => 'time_picker',
        'display_format' => 'g:i a',
        'return_format' => 'g:i a',
    ];
}
