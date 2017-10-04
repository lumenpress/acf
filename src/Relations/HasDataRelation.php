<?php

namespace LumenPress\ACF\Relations;

use LumenPress\Nimble\Models\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasDataRelation extends HasMany
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
    public function __construct(Model $parent)
    {
        $this->parent = $parent;
        $this->foreignKey = $this->getForeignKey();
        $this->localKey = $parent->getKeyName();

        if ($this->foreignKey == null) {
            static::$constraints = false;
        }

        $instance = $this->newRelatedInstance();

        parent::__construct($instance->newQuery(), $parent, $this->foreignKey, $this->localKey);
    }

    public function isOptionsTable()
    {
        return $this->parent->getTable() === 'options';
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if ($this->isOptionsTable()) {
            $this->query->where('option_name', 'like', 'options_%');

            return;
        }

        parent::addConstraints();
    }

    protected function newRelatedInstance()
    {
        return tap(new Meta, function ($instance) {
            $instance->setTableThroughParentTable($this->parent->getTable());

            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->parent->getConnectionName());
            }
        });
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
