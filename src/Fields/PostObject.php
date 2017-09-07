<?php

namespace Lumenpress\ACF\Fields;

use Lumenpress\Fluid\Models\Post;

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
        'ui' => 1,
    ];

    public function post_type($type)
    {
        $this->setContentAttribute('post_type', $type);
    }

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (! is_numeric($this->metaValue)) {
            return;
        }

        return Post::find($this->metaValue);
    }
}
