<?php

namespace Lumenpress\ACF\Models;

use Lumenpress\ACF\Fields\Field;
use Lumenpress\Fluid\Models\AbstractPost as Post;
use Lumenpress\ACF\Concerns\HasContentAttributes;

abstract class AbstractPost extends Post
{
    use HasContentAttributes;

    public function __construct(array $attributes = [])
    {
        $this->addHidden([
            'ID',
            'post_title',
            'post_name',
            'post_type',
            'post_author',
            'post_content',
            'ping_status',
            'comment_status',
            'post_password',
            'pinged',
            'to_ping',
            'post_content_filtered',
            'post_mime_type',
            'comment_count',
            'post_date',
            'post_date_gmt',
            'post_modified',
            'post_modified_gmt',
            'post_parent',
            'menu_order',
            'guid',
            'post_excerpt',
            'post_status',
        ]);

        $this->append(array_keys($this->defaults));

        parent::__construct($attributes);

        $this->comment_status = 'closed';
        $this->ping_status = 'closed';

        foreach ($this->defaults as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __call($method, $parameters)
    {
        // if (array_key_exists($method, $this->aliases)) {
        //     $method = $this->aliases[$method];
        // }
        // if (isset($this->$method)) {
        //     $this->$method = array_shift($parameters);
        //     return $this;
        // }
        // d($method, $this->appends, in_array($method, $this->appends));
        // if (static::class == Lumenpress\ACF\Fields\Select::class) {
        //     d($this->appends);
        // }
        //
        if (in_array($method, $this->appends) || array_key_exists($method, $this->aliases)) {
            // d(static::class, $method, in_array($method, $this->appends));
            $this->$method = array_shift($parameters);

            return $this;
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return parent::newQuery()->orderBy('menu_order');
    }

    /**
     * [fields description].
     * @param  [type] $callable [description]
     * @return [type]           [description]
     */
    public function fields($callable = null)
    {
        $relation = $this->hasMany(Field::class, 'post_parent');
        if (is_callable($callable)) {
            if (! isset($this->relations['fields'])) {
                $this->setRelation('fields', $relation->get());
            }
            $callable($this->fields);

            return $this;
        }

        return $relation;
    }

    public function save(array $options = [])
    {
        $this->attributes['post_type'] = $this->postType;
        $this->post_content = serialize($this->post_content);

        return parent::save($options);
    }
}
