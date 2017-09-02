<?php 

namespace Lumenpress\ACF\Collections;

use Lumenpress\ACF\Fields\Field;
use Lumenpress\ACF\Fields\FlexibleLayout;
use Lumenpress\ACF\Fields\Repeater;
use Lumenpress\ACF\Fields\FlexibleContent;
use Lumenpress\ORM\Collections\AbstractCollection;

class FieldCollection extends AbstractCollection
{
    protected $loopIndex = 0;

    protected $layoutKey;

    /**
     * [__call description]
     * @param  [type] $type       [description]
     * @param  [type] $arguments [description]
     * @return [type]             [description]
     */
    public function __call($type, $arguments)
    {
        if ($type === 'flexible') {
            $type = 'flexible_content';
        }
        $name = $arguments[0];
        // $flag = false;
        foreach ($this->items as $index => $item) {
            if ($item->name == $name) {
                $item->order = $this->loopIndex;
                $this->loopIndex++;
                // if ($flag) {
                //     $this->extraItems[] = $item;
                //     unset($this->items[$index]);
                // }
                if ($item->type !== $type) {
                    $item->type = $type;
                    $item = $item->newFromBuilder((object)$item->getAttributes());
                }
                if ($type === 'clone') {
                    $item->setGroupKey($this->relatedParent->key);
                }
                $this->changedKeys[$name] = true;
                // $flag = true;
                return $this->items[$index] = $item;
            }
        }
        if ($className = Field::getClassNameByType($type)) {
            $item = new $className;
            $item->type = $type;
            // if ($this->layoutKey) {
            //     $field->setContentAttribute('parent_layout', $this->layoutKey);
            // }
            $item->label = ucwords(str_slug($name, ' '));
            $item->name = str_slug($name, '_');
            $item->order = $this->loopIndex;
            $this->loopIndex++;
            if ($type === 'clone') {
                $item->setGroupKey($this->relatedParent->key);
            }
            // $item->label = ucwords(str_slug($name, ' '));
            $this->changedKeys[$item->name] = true;
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

    public function drop()
    {
        foreach (func_get_args() as $name) {
            unset($this->$name);
        }
    }

    public function dropAll()
    {
        foreach ($this->items as $index => $item) {
            $this->extraItems[] = $item;
            unset($this->items[$index]);
        }
        $this->items = [];
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
        foreach ($this->items as $index => $item) {
            if (isset($this->changedKeys[$item->name])) {
                $item->post_parent = $this->relatedParent->id;
                if ($this->relatedParent instanceof FlexibleLayout) {
                    $item->setContentAttribute('parent_layout', $this->relatedParent->key);
                    // d($item, $this->relatedParent->key);
                }
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
