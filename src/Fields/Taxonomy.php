<?php

namespace LumenPress\ACF\Fields;

use LumenPress\Nimble\Models\Taxonomy as Tax;

class Taxonomy extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979abce766d8',
        // 'label' => 'Taxonomy',
        // 'name' => 'taxonomy',
        'type' => 'taxonomy',
        'taxonomy' => 'category',
        'field_type' => 'checkbox',
        'allow_null' => 0,
        'add_term' => 1,
        'save_terms' => 0,
        'load_terms' => 0,
        'return_format' => 'id',
        'multiple' => 0,
    ];

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (is_null(parent::getMetaValueAttribute($value))) {
            return;
        }

        if (is_array($this->metaValue)) {
            return array_filter(array_map(function ($value) {
                return Tax::where('taxonomy', $this->taxonomy)->where('term_id', $value)->first();
            }, $this->metaValue));
        }

        if (is_numeric($this->metaValue)) {
            return Tax::where('taxonomy', $this->taxonomy)->where('term_id', $this->metaValue)->first();
        }
    }
}
