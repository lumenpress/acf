<?php

namespace LumenPress\ACF\Fields;

use LumenPress\Nimble\Models\Post;

class PageLink extends PostObject
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
        'multiple' => 0,
    ];

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        $value = parent::getMetaValueAttribute($value);

        if (is_array($value)) {
            return array_map(function ($item) {
                return $item->link;
            }, $value);
        }

        if ($value instanceof Post) {
            return $value->link;
        }
    }
}
