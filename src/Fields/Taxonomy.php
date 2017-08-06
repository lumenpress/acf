<?php 

namespace Lumenpress\Models\Acf\Fields;

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
        'multiple' => 0
    ];
}