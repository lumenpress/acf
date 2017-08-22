<?php 

namespace Lumenpress\Acf\Models;

use Lumenpress\ORM\Models\Post;

class FieldGroup extends AbstractPost
{
    /**
     * [$updating description]
     * @var boolean
     */
    public $updating = false;

    /**
     * [$postType description]
     * @var string
     */
    protected $postType = 'acf-field-group';

    /**
     * [$with description]
     * @var array
     */
    protected $with = ['fields'];

    /**
     * [$appends description]
     * @var [type]
     */
    protected $appends = [
        'key',
        'title',
        'location',
        'position',
        'style',
        'label_placement',
        'instruction_placement',
        'hide_on_screen',
        'description',
        'active'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->post_name = uniqid('group_');
        $this->position = 'normal';
        $this->style = 'default';
        $this->label_placement = 'top';
        $this->instruction_placement = 'label';
        $this->post_status = 'publish';
    }

    /**
     * Accessor for key attribute.
     *
     * @return returnType
     */
    public function getKeyAttribute($value)
    {
        return $this->post_name;
    }

    /**
     * Accessor for post_title attribute.
     *
     * @return returnType
     */
    public function getTitleAttribute($value)
    {
        return $this->post_title;
    }

    /**
     * Mutator for title attribute.
     *
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['post_title'] = $value;
        $this->post_excerpt = md5($value);
    }

    public function title($value)
    {
        $this->setTitleAttribute($value);
    }

    /**
     * Accessor for location attribute.
     *
     * @return returnType
     */
    public function getLocationAttribute($value)
    {
        return $this->getContentAttribute('location', []);
    }

    /**
     * Mutator for location attribute.
     *
     * @return void
     */
    public function setLocationAttribute(array $value)
    {
        if ($this->updating) {
            $this->setContentAttribute('location', []);
        }
        $location = $this->location;
        $location[] = $value;
        $this->setContentAttribute('location', $location);
    }

    public function location($param, $operator = null, $value = null, $boolean = 'and')
    {
        $location = [];
        if (is_array($param)) {
            foreach (func_get_args() as $args) {
                if (!is_array($args)) {
                    return;
                }
                $location[] = call_user_func_array([$this, 'getLocationArguments'], $args);
            }
        } else {
            $location[] = $this->getLocationArguments($param, $operator, $value);
        }
        $this->setLocationAttribute($location);
    }

    /**
     * Accessor for position attribute.
     *
     * @return returnType
     */
    public function getPositionAttribute($value)
    {
        return $this->getContentAttribute('position');
    }

    /**
     * Mutator for position attribute.
     *
     * @return void
     */
    public function setPositionAttribute($value)
    {
        $this->setContentAttribute('position', $value);
    }

    /**
     * Accessor for style attribute.
     *
     * @return returnType
     */
    public function getStyleAttribute($value)
    {
        return $this->getContentAttribute('style');
    }

    /**
     * Mutator for style attribute.
     *
     * @return void
     */
    public function setStyleAttribute($value)
    {
        $this->setContentAttribute('style', $value);
    }

    /**
     * Accessor for label placement attribute.
     *
     * @return returnType
     */
    public function getLabelPlacementAttribute($value)
    {
        return $this->getContentAttribute('label_placement');
    }

    /**
     * Mutator for label_placement attribute.
     *
     * @return void
     */
    public function setLabelPlacementAttribute($value)
    {
        $this->setContentAttribute('label_placement', $value);
    }
    /**
     * Accessor for instruction_placement attribute.
     *
     * @return returnType
     */
    public function getInstructionPlacementAttribute($value)
    {
        return $this->getContentAttribute('instruction_placement');
    }

    /**
     * Mutator for instruction_placement attribute.
     *
     * @return void
     */
    public function setInstructionPlacementAttribute($value)
    {
        $this->setContentAttribute('instruction_placement', $value);
    }

    /**
     * Accessor for hideOnScreen attribute.
     *
     * @return returnType
     */
    public function getHideOnScreenAttribute($value)
    {
        return $this->getContentAttribute('hide_on_screen');
    }

    /**
     * Mutator for hideOnScreen attribute.
     *
     * @return void
     */
    public function sethideOnScreenAttribute($value)
    {
        $this->setContentAttribute('hide_on_screen', $value);
    }

    /**
     * Accessor for description attribute.
     *
     * @return returnType
     */
    public function getDescriptionAttribute($value)
    {
        return $this->getContentAttribute('description');
    }

    /**
     * Mutator for description attribute.
     *
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->setContentAttribute('description', $value);
    }

    /**
     * Accessor for active attribute.
     *
     * @return returnType
     */
    public function getActiveAttribute($value)
    {
        return $this->post_status === 'publish';
    }

    public function showIn($object)
    {
        $bool = false;
        foreach ($this->location as $location) {
            foreach ($location as $args) {
                $bool = $object->locationRuleMatch($args['param'], $args['operator'], $args['value']);
            }
            if ($bool) {
                break;
            }
        }
        return $bool;
    }

    protected function getLocationArguments($param, $operator = null, $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '==';
        }
        $location['param'] = $param;
        $location['operator'] = $operator;
        $location['value'] = $value;
        return $location;
    }

    /**
     * [create description]
     * @param callable $callable [description]
     */
    public static function create($name, callable $callable)
    {
        $group = static::where('post_excerpt', md5($name))->first();

        if ($group) {
            throw new \Exception("The \"$name\" field group already exists.", 1);
        }

        $group = new static;
        $group->title = $name;
        $group->post_excerpt = md5($name);
        $callable($group);
        $group->save();

        return $group;
    }

    /**
     * [group description]
     * @param  [type]   $name     [description]
     * @param  callable $callable [description]
     * @return [type]             [description]
     */
    public static function group($name, callable $callable)
    {
        $group = static::where('post_excerpt', md5($name))->first();

        if (!$group) {
            throw new \Exception("\"$name\" field group does not exist.", 1);
        }

        $group->updating = true;
        $callable($group);
        $group->save();

        return $group;
    }

    public function exists($name)
    {
        return static::where('post_excerpt', md5($name))->count() > 0;
    }

    /**
     * [drop description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function drop($name)
    {
        return static::where('post_excerpt', md5($name))->delete();
    }

    /**
     * [createIfNotExist description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function createIfNotExist($name, callable $callable)
    {
        return !static::exists($name) ? static::create($name, $callable) : false;
    }
}
