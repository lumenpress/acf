<?php 

namespace Lumenpress\Acf\Collections;

use Illuminate\Database\Eloquent\Model;
use Lumenpress\ORM\Collections\AbstractCollection;
use Lumenpress\Acf\Fields\Field;

class FieldMetaCollection extends AbstractCollection
{

    protected $fields = [];

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_string($key)) {
            return !empty($this->offsetGet($key)->value);
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
                if (isset($this->fields[$key]) && $item->key == $key) {
                    $field = $this->fields[$key];
                    $field->value = $item->value;
                    return $field;
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
        if (isset($this->fields[$key])) {
            $field = $this->fields[$key];
            $field->value = $value;
            $this->items[] = $this->fields[$key] = $field;
        }
    }

    public function has($key)
    {
        if (is_string($key)) {
            return isset($this->fields[$key]);
        }
        return parent::has($key);
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * [save description]
     * @param  [type] $objectId [description]
     * @return [type]           [description]
     */
    public function save()
    {
        // foreach ( as $key => $field) {
        //     d($this->$key);
        // }

        foreach ($this->items as $index => $item) {
            if ($item instanceof Field) {
                $item->updateValue($this->object);
            } else {
                unset($this->items[$index]);
            }
        }
        $this->items = array_values($this->items);

        // $flag = false;
        // foreach ($this->items as $item) {
        //     if (isset($this->changedKeys[$item->key])) {
        //         $item->objectId = $objectId;
        //         $flag = $item->save() || $flag;
        //     }
        // }
        // foreach ($this->extraItems as $item) {
        //     $flag = $item->delete() || $flag;
        // }
        // $this->changedKeys = [];
        // $this->extraItems = [];
        // return $flag;
    }
}
