<?php

namespace LumenPress\ACF\Relations;

use LumenPress\ACF\Models\OptionField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasOptionFields extends Relation
{
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
        parent::__construct($query, $parent);
    }

    protected function newRelatedInstance()
    {
        return tap(new OptionField, function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->parent->getConnectionName());
            }
        });
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->query->where('option_name', 'like', 'options_%');
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {

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

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        return $models;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
    }
}
