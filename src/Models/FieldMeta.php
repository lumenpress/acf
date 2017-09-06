<?php

namespace Lumenpress\ACF\Models;

use Lumenpress\ORM\Models\Meta;
use Lumenpress\ACF\Collections\FieldMetaCollection;

class FieldMeta extends Meta
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
        return (new FieldMetaCollection($models))->setRelated($this);
    }
}
