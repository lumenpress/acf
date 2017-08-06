<?php 

namespace Lumenpress\Models\Acf;

use Illuminate\Support\Str;

abstract class AbstractField extends AbstractPost
{
    use Concerns\FieldDefaultAttributes;

    /**
     * [$types description]
     * @var array
     */
    protected static $types = [
        'clone' => Fields\CloneField::class,
        'flexible' => Fields\FlexibleContent::class,
    ];

    /**
     * [$postType description]
     * @var string
     */
    protected $postType = 'acf-field';

    /**
     * [$appends description]
     * @var [type]
     */
    protected $appends = [
        'key',
        'label',
        'name',
        'type',
        'instructions',
        'required',
        'conditional_logic',
        'wrapper',
        'full_name',
        'value',
    ];

    /**
     * [$defaults description]
     * @var [type]
     */
    protected $defaults = [];

    /**
     * [__construct description]
     * @param array $attributes [description]
     */
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

        $this->append(array_keys($this->defaults));

        parent::__construct($attributes);

        $this->type = 'text';
        $this->key = uniqid('field_');

        foreach ($this->defaults as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * [__call description]
     * @param  [type] $method     [description]
     * @param  [type] $parameters [description]
     * @return [type]             [description]
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->appends)) {
            $this->$method = array_shift($parameters);
            return $this;
        }
        return parent::__call($method, $parameters);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builders\FieldBuilder($query);
    }

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\Models\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new Collections\Fields($models);
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
        if ($attributes instanceof \stdClass) {
            if ($attributes->post_type !== 'acf-field') {
                $model = parent::newInstance([], true);
            } else {
                $content = is_serialized($attributes->post_content) 
                    ? unserialize($attributes->post_content) : [];
                $type = isset($content['type']) ? $content['type'] : 'text';
                $model = $this->newInstance([], true, static::getClass($type));
            }
        }

        $model->setRawAttributes((array) $attributes, true);
        $model->setConnection($connection ?: $this->getConnectionName());

        if (isset($type)) {
            switch ($type) {
                case 'repeater':
                    $model->setRelation('fields', $model->fields()->get());
                    break;
                
                case 'flexible_content':
                    $model->setRelation('fields', $model->fields()->get());
                    break;
            }
        }

        return $model;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false, $fieldClass = Fields\Text::class)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $fieldClass((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
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

    public function getLayoutKey()
    {
        return $this->getContentAttribute('parent_layout');
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
        if (array_key_exists($key, $this->defaults)) {
            $this->setContentAttribute($key, $value);
            return $this;
        }
        return parent::setAttribute($key, $value);
    }

    public function save(array $options = [])
    {
        return parent::save($options);
    }

    public static function toSpaceCase($value)
    {
        $pattern = '/(?<=[a-z])(?=[A-Z])/x';
        $arr = preg_split($pattern, $value);
        return join($arr, ' ');
    }

    public static function register($type, $className)
    {
        if (!class_exists($className)) {
            throw new \Exception("{$className} class doesn't exist.", 1);
        }
        static::$types[$type] = $className;
    }

    public static function getClass($type)
    {
        if (isset(static::$types[$type])) {
            return static::$types[$type];
        }

        $class = __NAMESPACE__.'\\Fields\\'.studly_case($type);

        return class_exists($class) ? $class : Fields\Text::class;
    }

    /**
     * [__toString description]
     * @return string [description]
     */
    public function __toString()
    {
        return is_string($value = $this->getValueAttribute(null)) ? $value : '';
    }

}
