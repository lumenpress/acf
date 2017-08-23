<?php

namespace Lumenpress\Acf\Models;

use Lumenpress\Acf\Fields\Field;
use Lumenpress\Acf\Concerns\ContentAttributes;
use Lumenpress\ORM\Models\AbstractPost as Post;

abstract class AbstractPost extends Post
{
    use ContentAttributes;

    public function __construct(array $attributes = [])
    {
        $this->addHidden([
            'ID',
            'post_title',
            'post_name',
            'post_type',
            'post_author',
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
        ]);

        $this->append(array_keys($this->defaults));

        parent::__construct($attributes);

        foreach ($this->defaults as $key => $value) {
            $this->$key = $value;
        }
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
     * [fields description]
     * @param  [type] $callable [description]
     * @return [type]           [description]
     */
    public function fields($callable = null)
    {
        $relation = $this->hasMany(Field::class, 'post_parent');
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

        $this->fields->save();

        return parent::save();
    }

}
