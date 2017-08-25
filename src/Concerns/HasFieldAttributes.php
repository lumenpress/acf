<?php 

namespace Lumenpress\Acf\Concerns;

use Illuminate\Support\Str;
use Lumenpress\Acf\Fields\Field;
use Lumenpress\Acf\Models\PostField;
use Lumenpress\Acf\Models\TermField;
use Lumenpress\Acf\Models\CommentField;
use Lumenpress\Acf\Models\UserField;
use Lumenpress\Acf\Models\OptionField;

trait HasFieldAttributes
{
    /**
     * [$relatedParent description]
     * @var [type]
     */
    protected $relatedParent;

    /**
     * [$metaKey description]
     * @var [type]
     */
    protected $metaKey;

    /**
     * [$metaValue description]
     * @var [type]
     */
    protected $metaValue;

    /**
     * [setRelatedParent description]
     * @param [type] &$relatedParent [description]
     */
    public function setRelatedParent(&$relatedParent)
    {
        $this->relatedParent = $relatedParent;
        $this->append('meta_key', 'meta_value');
        return $this;
    }

    /**
     * [setPostExcerptAttribute description]
     * @param [type] $value [description]
     */
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

    /**
     * [updateValue description]
     * @return [type] [description]
     */
    public function updateValue()
    {
        if (is_null($this->relatedParent)) {
            return false;
        }

        $className = null;
        $objectKey = null;
        
        switch ($this->relatedParent->getTable()) {
            case 'posts':
                $className = PostField::class;
                $objectKey = 'post_id';
                break;
            case 'terms':
                $className = TermField::class;
                $objectKey = 'term_id';
                break;
            case 'comments':
                $className = CommentField::class;
                $objectKey = 'comment_id';
                break;
            case 'users':
                $className = UserField::class;
                $objectKey = 'user_id';
                break;
            default:
                $className = OptionField::class;
                break;
        }

        $meta = $className::where('meta_key', $this->meta_key)
            ->where($objectKey, $this->relatedParent->id)->first();

        if (is_null($meta)) {
            $meta = new $className;
            $meta->objectId = $this->relatedParent->id;
        }

        $meta->key = $this->metaKey;
        $meta->value = is_array($this->metaValue) ? serialize($this->metaValue) : $this->metaValue;

        return $meta->save();
    }
}
