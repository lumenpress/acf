<?php

namespace LumenPress\ACF\Models;

use LumenPress\Nimble\Models\Option;
use LumenPress\ACF\Collections\MetaFieldCollection;

class OptionField extends Option
{
    public function newCollection(array $models = [])
    {
        return (new MetaFieldCollection($models))->setRelated($this);
    }
}
