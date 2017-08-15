<?php 

namespace Lumenpress\Acf\Concerns;

trait ContentAttributes
{

    /**
     * Accessor for content attribute.
     *
     * @return returnType
     */
    public function getPostContentAttribute($value)
    {
        return is_serialized($value) ? unserialize($value) : $value;
    }

    /**
     * Mutator for post content attribute.
     *
     * @return void
     */
    public function setPostContentAttribute($value)
    {
        $this->attributes['post_content'] = $value;
    }

    /**
     * Accessor for content attribute.
     *
     * @return returnType
     */
    public function getContentAttribute($key, $default = '')
    {
        return isset($this->post_content[$key]) ? $this->post_content[$key] : $default;
    }

    /**
     * Mutator for postContent attribute.
     *
     * @return void
     */
    public function setContentAttribute($key, $value = '')
    {
        if (!is_array($this->post_content)) {
            $this->attributes['post_content'] = [];
        } else {
            $this->attributes['post_content'] = $this->post_content;
        }
        $this->attributes['post_content'][$key] = $value;
    }

}
