<?php 

namespace Lumenpress\Models\Acf\Fields;

class Image extends File
{
    protected $defaults = [
        // 'key' => 'field_5979aad09eb5f',
        // 'label' => 'Image',
        // 'name' => 'image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'thumbnail',
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
