<?php 

namespace Lumenpress\Models\Acf\Fields;

class Gallery extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979aae5ae3e6',
        // 'label' => 'Gallery',
        // 'name' => 'gallery',
        'type' => 'gallery',
        'min' => '',
        'max' => '',
        'insert' => 'append',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => ''
    ];
}