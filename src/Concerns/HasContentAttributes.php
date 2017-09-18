<?php

namespace LumenPress\ACF\Concerns;

use Illuminate\Support\Str;
use LumenPress\Nimble\Concerns\TrySerialize;

trait HasContentAttributes
{
    use TrySerialize;

    /**
     * [$defaults description].
     * @var array
     */
    protected $defaults = [];

    /**
     * Mutator for post name attribute.
     *
     * @return void
     */
    public function setPostNameAttribute($value)
    {
        $this->attributes['post_name'] = $value;
    }

    /**
     * Mutator for postTitle attribute.
     *
     * @return void
     */
    public function setPostTitleAttribute($value)
    {
        $this->attributes['post_title'] = $value;
        if (! $this->post_excerpt) {
            $this->post_excerpt = str_slug($value, '_');
        }
    }

    /**
     * Accessor for content attribute.
     *
     * @return returnType
     */
    public function getPostContentAttribute($value)
    {
        return $this->trySerialize($value);
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
        if (! is_array($this->post_content)) {
            $this->attributes['post_content'] = [];
        } else {
            $this->attributes['post_content'] = $this->post_content;
        }
        $this->attributes['post_content'][$key] = $value;
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        if (method_exists($this, 'get'.Str::studly($key).'Attribute')) {
            return true;
        }
        if (array_key_exists($key, $this->defaults)) {
            return true;
        }

        return false;
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        if (method_exists($this, 'get'.Str::studly($key).'Attribute')) {
            return $this->{'get'.Str::studly($key).'Attribute'}($value);
        }
        if (array_key_exists($key, $this->defaults)) {
            return $this->getContentAttribute($key, $value);
        }
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set'.Str::studly($key).'Attribute';

            return $this->{$method}($value);
        }
        if (array_key_exists($key, $this->defaults)) {
            $this->setContentAttribute($key, $value);

            return $this;
        }

        return parent::setAttribute($key, $value);
    }
}
