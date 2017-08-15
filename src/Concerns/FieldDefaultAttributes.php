<?php 

namespace Lumenpress\Acf\Concerns;

use Lumenpress\Acf\Models\PostAcf;
use Illuminate\Database\Eloquent\Model;

trait FieldDefaultAttributes
{

    public $rawValue = '';

    public $fullName = '';

    public $object;

    /**
     * Accessor for key attribute.
     *
     * @return returnType
     */
    public function getKeyAttribute($value)
    {
        return $this->post_name;
    }

    /**
     * Mutator for key attribute.
     *
     * @return void
     */
    public function setKeyAttribute($value)
    {
        $this->attributes['post_name'] = $value;
    }

    /**
     * Accessor for label attribute.
     *
     * @return returnType
     */
    public function getLabelAttribute($value)
    {
        return $this->post_title;
    }

    /**
     * Mutator for label attribute.
     *
     * @return void
     */
    public function setLabelAttribute($value)
    {
        $this->attributes['post_title'] = $value;
    }

    /**
     * Accessor for name attribute.
     *
     * @return returnType
     */
    public function getNameAttribute($value)
    {
        return $this->post_excerpt;
    }

    /**
     * Mutator for name attribute.
     *
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['post_excerpt'] = $value;
    }

    /**
     * Accessor for type attribute.
     *
     * @return returnType
     */
    public function getTypeAttribute($value)
    {
        return $this->getContentAttribute('type');
    }

    /**
     * Mutator for type attribute.
     *
     * @return void
     */
    public function setTypeAttribute($value)
    {
        $this->setContentAttribute('type', $value);
    }

    /**
     * Accessor for instructions attribute.
     *
     * @return returnType
     */
    public function getInstructionsAttribute($value)
    {
        return $this->getContentAttribute('instructions');
    }

    /**
     * Mutator for instructions attribute.
     *
     * @return void
     */
    public function setInstructionsAttribute($value)
    {
        $this->setContentAttribute('instructions', $value);
    }

    /**
     * Accessor for required attribute.
     *
     * @return returnType
     */
    public function getRequiredAttribute($value)
    {
        return $this->getContentAttribute('required', 0);
    }

    /**
     * Mutator for required attribute.
     *
     * @return void
     */
    public function setRequiredAttribute($value)
    {
        $this->setContentAttribute('required', $value);
    }

    /**
     * Accessor for conditional_logic attribute.
     *
     * @return returnType
     */
    public function getConditionalLogicAttribute($value)
    {
        return $this->getContentAttribute('conditional_logic', 0);
    }

    /**
     * Mutator for conditional logic attribute.
     *
     * @return void
     */
    public function setConditionalLogicAttribute($value)
    {
        $this->setContentAttribute('conditional_logic', $value);
    }

    /**
     * Accessor for wrapper attribute.
     *
     * @return returnType
     */
    public function getWrapperAttribute($value)
    {
        return $this->getContentAttribute('wrapper', [
            'width' => '', 'class' => '', 'id' => ''
        ]);
    }

    /**
     * Mutator for wrapper attribute.
     *
     * @return void
     */
    public function setWrapperAttribute($value)
    {
        $this->setContentAttribute('wrapper', $value);
    }

    /**
     * Accessor for parent layout attribute.
     *
     * @return returnType
     */
    public function getParentLayoutAttribute($value)
    {
        return $this->getContentAttribute('parent_layout');
    }

    /**
     * Mutator for parent layout attribute.
     *
     * @return void
     */
    public function setParentLayoutAttribute($value)
    {
        $this->setContentAttribute('parent_layout', $value);
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        return $this->rawValue;
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (!$this->fullName) {
            $this->fullName = $this->name;
        }
        $this->rawValue = $value;
    }

    /**
     * Accessor for fullName attribute.
     *
     * @return returnType
     */
    public function getFullNameAttribute($value)
    {
        return $this->fullName;
    }

    public function updateValue(Model $object)
    {
        if (!is_array($this->value)) {
            $meta = PostAcf::where('meta_key', $this->fullName)->where('post_id', $object->getKey())->first();
            if (!$meta) {
                $meta = new PostAcf;
            }
            $meta->key = $this->fullName;
            $meta->value = $this->rawValue;
            $meta->objectId = $object->getKey();
            $meta->save();
        } else {
            $meta = PostAcf::where('meta_key', $this->fullName)->where('post_id', $object->getKey())->first();
            if (!$meta) {
                $meta = new PostAcf;
            }
            $meta->key = $this->fullName;
            $meta->value = count($this->value);
            $meta->objectId = $object->getKey();
            $meta->save();
            foreach ($this->value as $index => $item) {
                foreach ($item as $key => $field) {
                    $field->updateValue($object);
                }
            }
        }
    }
}