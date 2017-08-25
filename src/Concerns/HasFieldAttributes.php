<?php 

namespace Lumenpress\Acf\Concerns;

use Illuminate\Support\Str;
use Lumenpress\Acf\Fields\Field;

trait HasFieldAttributes
{
    protected $metaKey;

    protected $metaValue;

    public function setPostExcerptAttribute($value)
    {
        $this->attributes['post_excerpt'] = $value;
    }

    /**
     * Accessor for parent_layout attribute.
     *
     * @return returnType
     */
    public function getParentLayoutAttribute($value)
    {
        return $this->getContentAttribute('parent_layout');
    }

    /**
     * Mutator for parent_layout attribute.
     *
     * @return void
     */
    public function setParentLayoutAttribute($value)
    {
        $this->setContentAttribute('parent_layout', $value);
    }

    /**
     * Accessor for fullName attribute.
     *
     * @return returnType
     */
    public function getMetaKeyAttribute($value)
    {
        if (!$this->relatedParent) {
            return;
        }
        if (!$this->metaKey) {
            $this->metaKey = $this->name;
        }
        return $this->metaKey;
    }

    /**
     * Mutator for fullName attribute.
     *
     * @return void
     */
    public function setMetaKeyAttribute($value)
    {
        $this->metaKey = $value;
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if (is_null($this->metaValue)) {
            if (is_null($this->relatedParent)) {
                return;
            }
            $this->metaValue = $this->relatedParent->meta->{$this->name};
        }
        return $this->metaValue;
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($value)
    {
        $this->metaValue = $value;
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        return $this->getMetaValueAttribute($value);
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->setMetaValueAttribute($value);
    }

}
