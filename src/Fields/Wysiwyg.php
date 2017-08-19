<?php 

namespace Lumenpress\Acf\Fields;

class Wysiwyg extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979aa58d5dc4',
        // 'label' => 'Wysiwyg',
        // 'name' => 'wysiwyg',
        'type' => 'wysiwyg',
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 1,
        'delay' => 0
    ];

    /**
     * Accessor for value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        return luemnpress_get_the_content($this->rawValue);
    }

    // *
    //  * Mutator for value attribute.
    //  *
    //  * @return void
     
    // public function setValueAttribute($value)
    // {
    //     $this->rawValue = $value;
    //     // $this->rawValue = apply_filters( 'the_content', $value );
    // }
}
