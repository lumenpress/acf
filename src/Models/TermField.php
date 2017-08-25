<?php 

namespace Lumenpress\Acf\Models;

use Lumenpress\ORM\Models\TermMeta;
use Lumenpress\Acf\Collections\FieldMetaCollection;

class TermField extends TermMeta
{
    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\Database\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return FieldMetaCollection::create($models, static::class);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    // public function newEloquentBuilder($query)
    // {
    //     return new FieldBuilder($query);
    // }
}