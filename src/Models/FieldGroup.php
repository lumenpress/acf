<?php 

namespace Lumenpress\Acf\Models;

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
        'active' => 1,
    ];

    protected $aliases = [
        'id' => 'ID',
        'title' => 'post_title',
        'name' => 'post_excerpt',
        'key' => 'post_name',
        'order' => 'menu_order',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->post_name = uniqid('group_');
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
            $location[] = $this->locationAttributesToArray($param, $operator, $value);
        }
        $this->setLocationAttribute($location);
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
    public function setLocationAttribute(array $value)
    {
        if ($this->LocationIsBeingUpdated) {
            $this->setContentAttribute('location', []);
        }
        $location = $this->location;
        $location[] = $value;
        $this->setContentAttribute('location', $location);
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
