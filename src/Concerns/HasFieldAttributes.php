<?php 

namespace Lumenpress\Acf\Concerns;

use Illuminate\Support\Str;
use Lumenpress\Acf\Fields\Field;

trait HasFieldAttributes
{
    protected $fullName = '';

    protected $rawValue = '';

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
    public function getFullNameAttribute($value)
    {
        if (!$this->fullName) {
            $this->fullName = $this->name;
        }
        return $this->fullName;
    }

    /**
     * Mutator for fullName attribute.
     *
     * @return void
     */
    public function setFullNameAttribute($value)
    {
        $this->fullName = $value;
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
        $this->rawValue = $value;
    }

}
