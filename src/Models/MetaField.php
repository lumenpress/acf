<?php

namespace LumenPress\ACF\Models;

use LumenPress\Nimble\Models\Meta;
use LumenPress\ACF\Collections\MetaFieldCollection;

class MetaField extends Meta
{
    public function newCollection(array $models = [])
    {
        return (new MetaFieldCollection($models))->setRelated($this);
    }
}
