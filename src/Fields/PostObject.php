<?php

namespace LumenPress\ACF\Fields;

use LumenPress\Nimble\Models\Post;

class PostObject extends Field
{
    protected $post_type;

    protected $defaults = [
        'type' => 'post_object',
        'post_type' => [],
        'taxonomy' => [],
        'allow_null' => 0,
        'multiple' => 0,
        'return_format' => 'object',
        'ui' => 1,
    ];

    public function __construct(array $attributes = [])
    {
        $this->post_type = $this->postType;
        $this->setContentAttribute('post_type', $this->defaults['post_type']);

        unset($this->defaults['post_type']);

        parent::__construct($attributes);
    }

    /**
     * Accessor for postType attribute.
     *
     * @return returnType
     */
    public function getPostTypeAttribute($value)
    {
        return $this->getContentAttribute('post_type');
    }

    /**
     * Mutator for postType attribute.
     *
     * @return void
     */
    public function setPostTypeAttribute($value)
    {
        if (is_array($value)) {
            $this->setContentAttribute('post_type', $value);
        }
    }

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (is_null(parent::getMetaValueAttribute($value))) {
            return;
        }

        if (is_array($this->metaValue)) {
            return array_filter(array_map(function ($value) {
                return is_numeric($value) ? Post::find($value) : null;
            }, $this->metaValue));
        }

        if (! is_numeric($this->metaValue)) {
            return;
        }

        return Post::find($this->metaValue);
    }
}
