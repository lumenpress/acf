<?php

namespace LumenPress\ACF\Collections;

use LumenPress\ACF\Fields\Field;
use LumenPress\Nimble\Collections\Collection;

class FieldMetaCollection extends Collection
{
    public function offsetExists($key)
    {
        return ! is_null($this->offsetGet($key));
    }

    public function offsetGet($key)
    {
        if (isset($this->items[$key])) {
            if ($this->items[$key] instanceof Field) {
                return $this->items[$key]->value;
            }
        }
    }

    public function offsetSet($key, $value)
    {
        if (isset($this->items[$key])) {
            $this->changedKeys[$key] = true;
            $this->items[$key]->value = $value;
        } else {
            throw new \Exception("{$key} Field not exists.", 1);
        }
    }

    public function offsetUnset($key)
    {
        $this->extraItems[] = $this->items[$key];
        unset($this->items[$key]);
    }

    public function setRelatedParent(&$relatedParent)
    {
        parent::setRelatedParent($relatedParent);
        $this->items = $relatedParent->getAcfFieldObjects();

        return $this;
    }

    public function save()
    {
        if (! $this->relatedParent) {
            return false;
        }
        $flag = false;
        foreach ($this->items as $key => $item) {
            if (isset($this->changedKeys[$key])) {
                $flag = $item->updateValue() || $flag;
            }
        }
        foreach ($this->extraItems as $item) {
            $flag = $item->deleteValue() || $flag;
        }
        $this->changedKeys = [];
        $this->extraItems = [];

        return $flag;
    }
}
