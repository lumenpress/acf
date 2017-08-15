<?php 

namespace Lumenpress\Acf\Fields;

use Illuminate\Database\Eloquent\Model;
use Lumenpress\Acf\Models\PostAcf;

class File extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979aad89eb60',
        // 'label' => 'File',
        // 'name' => 'file',
        'type' => 'file',
        'return_format' => 'array',
        'library' => 'all',
        'min_size' => '',
        'max_size' => '',
        'mime_types' => ''
    ];

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
        return $this;
    }

    /**
     * Accessor for Value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        if (!$this->rawValue) {
            return;
        }

        if (is_numeric($this->rawValue)) {
            return wp_get_attachment_url( $this->rawValue );
        }

        if(preg_match('@^//@', $this->rawValue) || filter_var($this->rawValue, FILTER_VALIDATE_URL) !== false) {
            return $this->rawValue;
        }

        if (file_exists($this->rawValue)) {
            return 'data:image/' . pathinfo($this->rawValue, PATHINFO_EXTENSION) 
                . ';base64,' . base64_encode(file_get_contents($this->rawValue));
        }

        if (file_exists(config('wordpress.assets.base_path').$this->rawValue)) {
            return config('wordpress.assets.base_url').$this->rawValue;
        }
    }

    public function updateValue(Model $object)
    {
        if (!$this->rawValue) {
            return false;
        }

        if (!is_numeric($this->rawValue) && file_exists(config('wordpress.assets.base_path').$this->rawValue)) {
            $this->rawValue = wpp_insert_asset($this->rawValue);
        }

        if (is_numeric($this->rawValue)) {
            $meta = PostAcf::where('meta_key', $this->fullName)->where('post_id', $object->getKey())->first();
            if (!$meta) {
                $meta = new PostAcf;
            }
            $meta->key = $this->fullName;
            $meta->value = $this->rawValue;
            $meta->objectId = $object->getKey();
            return $meta->save();
        }

        return false;
    }

}
