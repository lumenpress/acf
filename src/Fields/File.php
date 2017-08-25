<?php 

namespace Lumenpress\Acf\Fields;

use Illuminate\Database\Eloquent\Model;
use Lumenpress\Acf\Models\PostAcf;

class File extends Field
{
    protected $defaults = [
        'type' => 'file',
        'return_format' => 'array',
        'library' => 'all',
        'min_size' => '',
        'max_size' => '',
        'mime_types' => ''
    ];

    public function getValueAttribute($value)
    {
        return parent::getValueAttribute($value);
        // if (!$this->rawValue) {
        //     return;
        // }

        // if (is_numeric($this->rawValue)) {
        //     return lumenpress_get_attachment_url($this->rawValue);
        // }

        // if(lumenpress_asset_url($this->rawValue)) {
        //     return $this->rawValue;
        // }

        // if (file_exists($this->rawValue)) {
        //     return 'data:image/' . pathinfo($this->rawValue, PATHINFO_EXTENSION) 
        //         . ';base64,' . base64_encode(file_get_contents($this->rawValue));
        // }

        // if (file_exists(config('wordpress.assets.base_path').$this->rawValue)) {
        //     return config('wordpress.assets.base_url').$this->rawValue;
        // }
    }

    public function updateValue(Model $object)
    {
        if (!$this->rawValue) {
            return false;
        }

        if (!is_numeric($this->rawValue) && file_exists(config('wordpress.assets.base_path').$this->rawValue)) {
            $this->rawValue = lumenpress_insert_asset($this->rawValue);
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
