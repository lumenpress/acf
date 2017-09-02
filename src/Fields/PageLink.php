<?php 

namespace Lumenpress\ACF\Fields;

class PageLink extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979abae766d6',
        // 'label' => 'Page Link',
        // 'name' => 'page_link',
        'type' => 'page_link',
        'post_type' => [],
        'taxonomy' => [],
        'allow_null' => 0,
        'allow_archives' => 1,
        'multiple' => 0
    ];
}
