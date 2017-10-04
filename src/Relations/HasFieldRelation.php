<?php

namespace LumenPress\ACF\Relations;

use LumenPress\ACF\Models\FieldMeta;

class HasFieldRelation extends HasDataRelation
{
    protected function newRelatedInstance()
    {
        if ($this->isOptionsTable()) {
            return $this->parent;
        }

        return tap(new FieldMeta, function ($instance) {
            $instance->setTableThroughParentTable($this->parent->getTable());

            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->parent->getConnectionName());
            }
        });
    }
}
