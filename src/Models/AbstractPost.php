<?php

namespace Lumenpress\Acf\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as AbstractModel;
use Lumenpress\Acf\Concerns\ContentAttributes;

abstract class AbstractPost extends AbstractModel
{
    use ContentAttributes;

    const CREATED_AT = 'post_date';

    const UPDATED_AT = 'post_modified';

    /**
     * [$table description]
     * @var string
     */
    protected $table = 'posts';

    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * [$foreignKey description]
     * @var string
     */
    protected $foreignKey = 'post_id';

    /**
     * [$dates description]
     * @var [type]
     */
    protected $dates = [
        'post_date', 
        'post_date_gmt', 
        'post_modified', 
        'post_modified_gmt'
    ];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = [
        'post_title',
        'post_name',
        'post_excerpt',
        // 'post_content',
        'post_parent',
        'post_status',
        'guid',
        'post_date_gmt',
        'post_date',
        'post_modified',
        'post_modified_gmt',
        'post_author',
        'comment_count',
        'post_mime_type',
        'post_type',
        'ping_status',
        'comment_status',
        'post_password',
        'pinged',
        'to_ping',
        'post_content_filtered'
    ];

    /**
     * [__construct description]
     * @param array $attributes [description]
     */
    public function __construct(array $attributes = [])
    {
        if (!$this->postType) {
            throw new \Exception("This postType is not declared.", 1);
        }

        parent::__construct($attributes);

        $this->post_type = $this->postType;
        $this->ID = 0;
        $this->post_parent = 0;
        $this->menu_order = 0;
        $this->post_status = 'publish';
        $this->comment_status = 'closed';
        $this->post_author = (int) get_current_user_id();
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        $builder = parent::newQuery();

        $builder->where('post_type', $this->postType)->orderBy('menu_order');

        return $builder;
    }

    /**
     * [fields description]
     * @param  [type] $callable [description]
     * @return [type]           [description]
     */
    public function fields($callable = null)
    {
        $relation = $this->hasMany(Fields\Field::class, 'post_parent');
        if (is_callable($callable)) {
            if (!isset($this->relations['fields'])) {
                $this->relations['fields'] = $relation->get();
            }
            if (property_exists($this, 'currentLayoutKey') and $this->currentLayoutKey) {
                $this->fields->setLayoutKey($this->currentLayoutKey);
            }
            $callable($this->fields);
            return $this;
        }
        return $relation;
    }

    public function save(array $options = [])
    {
        $this->post_content = serialize($this->post_content);

        if (!parent::save($options)) {
            return false;
        }

        if (!$this->post_date_gmt) {
            $this->post_date_gmt = $this->post_date->tz('UTC');
        }

        $this->post_modified_gmt = $this->post_modified->tz('UTC');
        $this->guid = get_permalink($this->ID);

        if (in_array('fields', $this->with)) {
            $this->fields->save($this);
        }

        return parent::save();
    }

}
