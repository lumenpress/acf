<?php

namespace Lumenpress\ACF\Fields;

use Carbon\Carbon;

class DateTimePicker extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ac03766dc',
        // 'label' => 'Date Time Picker',
        // 'name' => 'date_time_picker',
        'type' => 'date_time_picker',
        'display_format' => 'd/m/Y g:i a',
        'return_format' => 'd/m/Y g:i a',
        'first_day' => 1,
    ];

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        parent::getMetaValueAttribute($value);

        if (!$this->metaValue) {
            return;
        }
        try {
            return Carbon::parse($this->metaValue)->format($this->return_format);
        } catch (\Exception $e) {
            return;
        }
    }
}
