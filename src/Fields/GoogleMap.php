<?php 

namespace Lumenpress\Acf\Fields;

class GoogleMap extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979abe8766da',
        // 'label' => 'Google Map',
        // 'name' => 'google_map',
        'type' => 'google_map',
        'center_lat' => '',
        'center_lng' => '',
        'zoom' => '',
        'height' => ''
    ];
}