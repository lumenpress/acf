<?php 

namespace Lumenpress\ACF\Fields;

class User extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979abd9766d9',
        // 'label' => 'User',
        // 'name' => 'user',
        'type' => 'user',
        'role' => '',
        'allow_null' => 0,
        'multiple' => 0
    ];
}