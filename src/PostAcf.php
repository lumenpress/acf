<?php 

namespace Lumenpress\Models\Acf;

use Lumenpress\Models\PostMeta;
use Lumenpress\Models\Acf\Builders\PostAcfBuilder;
use Lumenpress\Models\Acf\Collections\FieldCollection;

class PostAcf extends PostMeta
{
    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\Models\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return FieldCollection::create($models, static::class, $this);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new PostAcfBuilder($query);
    }
}
