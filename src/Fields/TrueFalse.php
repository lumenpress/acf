<?php

namespace LumenPress\ACF\Fields;

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
        'ui_off_text' => '',
    ];

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($value)
    {
        if (! is_null($value)) {
            $value = $value ? 1 : 0;
        }
        parent::setMetaValueAttribute($value);
    }
}
