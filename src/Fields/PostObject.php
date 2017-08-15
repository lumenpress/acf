<?php 

namespace Lumenpress\Acf\Fields;

class PostObject extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ab83f45b7',
        // 'label' => 'Post Object',
        // 'name' => 'post_object',
        'type' => 'post_object',
        'post_type' => [],
        'taxonomy' => [],
        'allow_null' => 0,
        'multiple' => 0,
        'return_format' => 'object',
        'ui' => 1
    ];
}