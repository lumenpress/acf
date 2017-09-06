<?php

namespace Lumenpress\ACF\Fields;

class Relationship extends Field
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
}
