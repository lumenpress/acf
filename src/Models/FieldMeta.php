<?php

namespace LumenPress\ACF\Models;

use LumenPress\Nimble\Models\Meta;
use LumenPress\ACF\Collections\FieldMetaCollection;

class FieldMeta extends Meta
{
    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \LumenPress\Database\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return (new FieldMetaCollection($models))->setRelated($this);
    }
}
