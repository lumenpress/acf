<?php 

namespace Lumenpress\Acf\Models;

use Illuminate\Support\Str;
use Lumenpress\Acf\Fields\Field;
use Lumenpress\Acf\Fields\Text;
use Lumenpress\Acf\Fields\CloneField;
use Lumenpress\Acf\Fields\FlexibleContent;
use Lumenpress\Acf\Builders\FieldBuilder;
use Lumenpress\Acf\Collections\Fields;
use Lumenpress\Acf\Concerns\HasFieldAttributes;
use Lumenpress\Acf\Collections\FieldCollection;

abstract class AbstractField extends AbstractPost
{
    use HasFieldAttributes;

    /**
     * [$types description]
     * @var array
     */
    protected static $types = [
        'clone' => CloneField::class,
        'flexible' => FlexibleContent::class,
    ];

    protected $postType = 'acf-field';

    protected $aliases = [
        'id' => 'ID',
        'label' => 'post_title',
        'name' => 'post_excerpt',
        'key' => 'post_name',
        'order' => 'menu_order',
    ];

    protected $appends = [
        'full_name',
    ];

    protected $hidden = [
        // 'parent'
    ];

    public function __construct(array $attributes = [])
    {
        $this->defaults = array_merge([
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => ''
            ],
        ], $this->defaults);

        parent::__construct($attributes);

        $this->type = 'text';
        $this->post_name = uniqid('field_');
    }

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\ORM\PostMetaCollection
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
    public function newInstance($attributes = [], $exists = false, $className = Text::class)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $className((array) $attributes);

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

        if ($attributes instanceof \stdClass) {
            $settings = is_serialized($attributes->post_content) 
                ? unserialize($attributes->post_content) : [];
        }

        $type = isset($settings['type']) ? $settings['type'] : 'text';

        $model = $this->newInstance([], true, static::getClassNameByType($type, Text::class));

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        switch ($type) {
            case 'repeater':
            case 'flexible_content':
                $model->setRelation('fields', $model->fields()->get());
                break;
        }

        unset($settings, $type);

        return $model;
    }

    public static function register($type, $className)
    {
        if (!class_exists($className)) {
            throw new \Exception("{$className} class doesn't exist.", 1);
        }
        static::$types[$type] = $className;
    }

    public static function getClassNameByType($type, $default = null)
    {
        if (isset(static::$types[$type])) {
            return static::$types[$type];
        }

        $class = 'Lumenpress\\Acf\\Fields\\'.studly_case($type);

        return class_exists($class) ? $class : $default;
    }

}
