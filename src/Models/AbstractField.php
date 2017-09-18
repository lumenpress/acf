<?php

namespace LumenPress\ACF\Models;

use LumenPress\ACF\Fields\Text;
use LumenPress\ACF\Fields\Field;
use LumenPress\ACF\Collections\Fields;
use LumenPress\ACF\Collections\FieldCollection;
use LumenPress\ACF\Concerns\HasFieldAttributes;

abstract class AbstractField extends AbstractPost
{
    use HasFieldAttributes;

    protected $postType = 'acf-field';

    protected $aliases = [
        'id' => 'ID',
        'label' => 'post_title',
        'name' => 'post_excerpt',
        'key' => 'post_name',
        'order' => 'menu_order',
    ];

    // protected $appends = [];

    // protected $hidden = [];

    public function __construct(array $attributes = [])
    {
        $this->defaults = array_merge([
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
            ],
        ], $this->defaults);

        parent::__construct($attributes);

        $this->type = 'text';
        $this->post_name = uniqid('field_');
    }

    public function __toString()
    {
        return is_string($this->value) || is_numeric($this->value) ? $this->value : '';
    }

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \LumenPress\Nimble\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new FieldCollection($models);
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $attributes = (array) $attributes;

        $type = isset($attributes['type']) ? $attributes['type'] : 'text';

        unset($attributes['type']);

        $class = static::getClassNameByType($type, Text::class);
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $class($attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $settings = [];
        $attributes = (array) $attributes;

        if (isset($attributes['post_content'])) {
            $settings = $this->trySerialize($attributes['post_content']);
        }

        $type = isset($settings['type']) ? $settings['type'] : 'text';

        $model = $this->newInstance(['type' => $type], true);

        $model->setRawAttributes($attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        switch ($type) {
            case 'repeater':
            case 'group':
                $model->setRelation('fields', $model->fields()->get());
                break;
        }

        unset($settings, $type);

        return $model;
    }
}
