<?php 

namespace Lumenpress\Acf\Collections;

use Lumenpress\Acf\Fields\Field;
use Lumenpress\ORM\Collections\AbstractCollection;

class FieldCollection extends AbstractCollection
{
    /**
     * [__call description]
     * @param  [type] $type       [description]
     * @param  [type] $arguments [description]
     * @return [type]             [description]
     */
    public function __call($type, $arguments)
    {
        $name = $arguments[0];
        foreach ($this->items as $key => $item) {
            if ($item->name == $name) {
                $this->changedKeys[$name] = true;
                return $item;
            }
        }
        if ($className = Field::getClassNameByType($type)) {
            $item = new $className;
            // if ($this->layoutKey) {
            //     $field->setContentAttribute('parent_layout', $this->layoutKey);
            // }
            $item->name = str_slug($name, '_');
            $item->label = ucwords(str_slug($name, ' '));
            $this->changedKeys[$name] = true;
            return $this->items[] = $item;
        }
        unset($name);
        return parent::__call($type, $arguments);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_string($key)) {
            foreach ($this->items as $item) {
                if ($item->name == $key) {
                    return true;
                }
            }
            return false;
        } else {
            return array_key_exists($key, $this->items);
        }
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (is_string($key)) {
            foreach ($this->items as $item) {
                if ($item->name == $key) {
                    return $item;
                }
            }
        } else {
            return $this->items[$key];
        }
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_string($key)) {
            $this->changedKeys[$key] = true;
            foreach ($this->items as $index => $item) {
                if ($item->name == $key) {
                    $item->name = $value;
                    parent::offsetSet($index, $item);
                    return;
                }
            }
            $class = $this->relatedClass;
            $item = new $class;
            $item->name = $key;
            $item->value = $value;
            parent::offsetSet(null, $item);
            return;
        }
        parent::offsetSet($key, $value);
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        if (is_string($key)) {
            foreach ($this->items as $index => $item) {
                if ($item->name == $key) {
                    $this->extraItems[] = $item;
                    unset($this->items[$index]);
                    return;
                }
            }
        } else {
            $this->extraItems[] = $this->items[$key];
            unset($this->items[$key]);
        }
    }

    public function rename($oldname, $newname)
    {
        if ($item = $this->$oldname) {
            $this->changedKeys[$newname] = true;
            $item->name = $newname;
            return $item;
        }
    }

    public function drop($name)
    {
        unset($this->$name);
    }

    /**
     * [save description]
     * @return [type] [description]
     */
    public function save()
    {
        if (!$this->relatedParent) {
            return false;
        }
        $flag = false;
        foreach ($this->items as $item) {
            if (isset($this->changedKeys[$item->name])) {
                $item->post_parent = $this->relatedParent->id;
                $flag = $item->save() || $flag;
            }
        }
        foreach ($this->extraItems as $item) {
            $flag = $item->delete() || $flag;
        }
        $this->changedKeys = [];
        $this->extraItems = [];
        return $flag;
    }
}
