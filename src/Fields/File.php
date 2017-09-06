<?php

namespace Lumenpress\ACF\Fields;

use Lumenpress\ORM\Models\Attachment;

class File extends Field
{
    protected $defaults = [
        'type' => 'file',
        'return_format' => 'array',
        'library' => 'all',
        'min_size' => '',
        'max_size' => '',
        'mime_types' => '',
    ];

    public function getMetaValueAttribute($value)
    {
        parent::getMetaValueAttribute($value);

        if (empty($this->metaValue)) {
            return;
        }

        if (is_numeric($this->metaValue)) {
            return wp_get_attachment_url($this->metaValue);
            $attachment = Attachment::find($this->metaValue);

            return $attachment ? $attachment->link : '';
        }

        if (lumenpress_is_url($this->metaValue)) {
            return $this->metaValue;
        }

        if (file_exists($file = lumenpress_asset_path($this->metaValue))) {
            return lumenpress_asset_url($this->metaValue);
            // return 'data:image/' . pathinfo($file, PATHINFO_EXTENSION)
            //     . ';base64,' . base64_encode(file_get_contents($file));
        }
    }

    /**
     * [updateValue description].
     * @return [type] [description]
     */
    public function updateValue()
    {
        if (is_string($this->metaValue)) {
            $this->metaValue = Attachment::upload($this->metaValue)->ID;
        }
        if (! is_numeric($this->metaValue)) {
            return;
        }

        return parent::updateValue();
    }
}
