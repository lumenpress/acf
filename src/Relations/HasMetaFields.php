<?php

namespace LumenPress\ACF\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasMetaFields extends HasMany
{
    protected $tmpData = [];

    /**
     * Create a new has one or many relationship instance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model   $parent
     * @param string                                $foreignKey
     * @param string                                $localKey
     *
     * @return void
     */
    public function __construct(Builder $query, Model $parent)
    {
        $this->parent = $parent;
        parent::__construct($query, $parent, $this->getForeignKey(), $parent->getKeyName());
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        // foreach ($models as $model) {
        //     $model->setRelation($relation, $this->related->newCollection());
        // }

        return $models;
    }

    protected function getForeignKey()
    {
        switch ($this->parent->getTable()) {
            case 'posts':
                return 'postmeta.post_id';
            case 'terms':
                return 'termmeta.term_id';
            case 'users':
                return 'usermeta.user_id';
            case 'comments':
                return 'commentmeta.comment_id';
        }
    }

    public function key($key = null)
    {
        switch ($this->parent->getTable()) {
            case 'posts':
                $objectKeyName = 'post_id';
                break;
            case 'terms':
                $objectKeyName = 'term_id';
                break;
            case 'users':
                $objectKeyName = 'user_id';
                break;
            case 'comments':
                $objectKeyName = 'comment_id';
                break;
        }
        $this->tmpData[$objectKeyName] = $this->getParentKey();
        $this->tmpData['meta_key'] = $key;
        $this->query->where('meta_key', $key);

        return $this;
    }

    public function value($value = null)
    {
        if (is_null($value)) {
            return $this->query->value('meta_value');
        }
        $this->tmpData['meta_value'] = $value;

        return $this;
    }

    public function push()
    {
        if ($this->query->first()) {
            return $this->query->update($this->tmpData);
        } else {
            return $this->query->insert($this->tmpData);
        }
    }
}
