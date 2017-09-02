<?php 

namespace Lumenpress\Acf\Models;

use Lumenpress\Acf\Builders\FieldGroupBuilder;

class FieldGroup extends AbstractPost
{
    public $LocationIsBeingUpdated = false;

    protected $postType = 'acf-field-group';

    protected $with = ['fields'];

    protected $defaults = [
        'location' => [],
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'description' => '',
    ];

    protected $aliases = [
        'id' => 'ID',
        'title' => 'post_title',
        'name' => 'post_excerpt',
        'key' => 'post_name',
        'order' => 'menu_order',
    ];

    protected $appends = [
        'active',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->post_name = uniqid('group_');
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return (new FieldGroupBuilder($query))->where('post_type', $this->postType);
    }

    /**
     * Accessor for active attribute.
     *
     * @return returnType
     */
    public function getActiveAttribute($value)
    {
        return $this->post_status == 'publish' ? 1 : 0;
    }

    /**
     * Mutator for active attribute.
     *
     * @return void
     */
    public function setActiveAttribute($value)
    {
        $this->attributes['active'] == $value ? 'publish' : 'acf-disabled';
    }

    /**
     * Mutator for location attribute.
     *
     * @return void
     */
    public function setLocationAttribute(array $location)
    {
        $this->setContentAttribute('location', $location);
    }

    public function location($param, $operator = null, $value = null, $boolean = 'and')
    {
        if ($this->LocationIsBeingUpdated) {
            $this->setContentAttribute('location', []);
            $this->LocationIsBeingUpdated = false;
        }

        $location = [];
        $locations = $this->location;

        if (is_array($param)) {
            // d($boolean, func_get_args());
            foreach (func_get_args() as $args) {
                if (!is_array($args)) {
                    return;
                }
                $location[] = call_user_func_array([$this, 'locationAttributesToArray'], $args);
            }
        } else {
            $location[] = $this->locationAttributesToArray($param, $operator, $value);
        }

        if ($boolean === 'and') {
            $last = array_pop($locations);
            $locations[] = is_null($last) ? $location : array_merge($last, $location);
        } else {
            $locations[] = $location;
        }
        
        $this->setLocationAttribute($locations);

        return $this;
    }

    public function orLocation($param, $operator = null, $value = null)
    {
        if (is_array($param)) {
            foreach (func_get_args() as $index => $args) {
                $this->location(array_shift($args), array_shift($args), array_shift($args), $index ? 'and' : 'or');
            }
            return $this;
        }
        return $this->location($param, $operator, $value, 'or');
    }

    public function locationRuleMatch($object)
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

    protected function locationAttributesToArray($param, $operator = null, $value = null)
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
}
