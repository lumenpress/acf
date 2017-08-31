<?php 

namespace Lumenpress\Acf\Concerns;

use Illuminate\Support\Str;
use Lumenpress\Acf\Fields\Field;
use Lumenpress\Acf\Models\FieldMeta;

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

        $meta = new FieldMeta;

        switch ($this->relatedParent->getTable()) {
            case 'posts':
                $table = 'postmeta';
                $objectKey = 'post_id';
                break;
            case 'terms':
                $table = 'termmeta';
                $objectKey = 'term_id';
                break;
            case 'comments':
                $table = 'commentmeta';
                $objectKey = 'comment_id';
                break;
            case 'users':
                $table = 'usermeta';
                $objectKey = 'user_id';
                break;
            // default:
            //     $query = \DB::table('options');
            //     $objectKey = 'option_id';
            //     break;
        }

        $meta->setTable($table);
        $meta->setObjectKeyName($objectKey);

        $meta->object_id = $this->relatedParent->id;
        $meta->key = $this->metaKey;
        $meta->value = is_array($this->metaValue) ? serialize($this->metaValue) : $this->metaValue;

        $result = \DB::table($table)->where('meta_key', $this->meta_key)
            ->where($objectKey, $this->relatedParent->id)->first();

        if ($result) {
            $meta->exists = true;
            $meta->id = $result->meta_id;
        }

        return $meta->save();
    }

    public function deleteValue()
    {
        if (is_null($this->relatedParent)) {
            return false;
        }

        switch ($this->relatedParent->getTable()) {
            case 'posts':
                $table = 'postmeta';
                $objectKey = 'post_id';
                break;
            case 'terms':
                $table = 'termmeta';
                $objectKey = 'term_id';
                break;
            case 'comments':
                $table = 'commentmeta';
                $objectKey = 'comment_id';
                break;
            case 'users':
                $table = 'usermeta';
                $objectKey = 'user_id';
                break;
            // default:
            //     $query = \DB::table('options');
            //     $objectKey = 'option_id';
            //     break;
        }

        return \DB::table($table)->where('meta_key', $this->meta_key)
            ->where($objectKey, $this->relatedParent->id)->delete();
    }
}
