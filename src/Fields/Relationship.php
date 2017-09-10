<?php

namespace LumenPress\ACF\Fields;

class Relationship extends PostObject
{
    protected $defaults = [
        // 'key' => 'field_5979abc1766d7',
        // 'label' => 'Relationship',
        // 'name' => 'relationship',
        'type' => 'relationship',
        'post_type' => [],
        'taxonomy' => [],
        'filters' => [
            'search',
            'post_type',
            'taxonomy',
        ],
        'elements' => '',
        'min' => '',
        'max' => '',
        'return_format' => 'object',
    ];

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (is_null($posts = parent::getMetaValueAttribute($value))) {
            return;
        }
        if (! is_array($this->metaValue)) {
            return;
        }

        return $posts;
    }
}
