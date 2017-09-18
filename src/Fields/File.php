<?php

namespace LumenPress\ACF\Fields;

use LumenPress\Nimble\Models\Attachment;

class File extends Field
{
    protected $attachment;

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
        if ($this->attachment) {
            return $this->attachment;
        }

        parent::getMetaValueAttribute($value);

        if (empty($this->metaValue)) {
            return;
        }

        if (is_numeric($this->metaValue)) {
            $this->attachment = Attachment::find($this->metaValue);
        } else {
            $this->attachment = Attachment::findBySrc($this->metaValue);
        }

        if (! $this->attachment) {
            $this->attachment = new Attachment;
            $this->attachment->file = $this->metaValue;
        }

        return $this->attachment;
    }

    /**
     * Mutator for metaValue attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($value)
    {
        if (is_numeric($value)) {
            $this->attachment = Attachment::find($value);
        } else {
            $this->attachment = Attachment::findBySrc($value);
        }

        if (! $this->attachment) {
            $this->attachment = new Attachment;
            $this->attachment->file = $value;
        }

        parent::setMetaValueAttribute($value);
    }

    /**
     * [updateValue description].
     * @return [type] [description]
     */
    public function updateValue()
    {
        if (! $this->attachment) {
            return false;
        }

        $this->attachment->save();

        $this->metaValue = $this->attachment->ID;

        return parent::updateValue();
    }
}
