<?php

namespace LumenPress\ACF\Fields;

class Message extends Field
{
    protected $defaults = [
        // 'key' => 'field_5979ac34766df',
        // 'label' => 'Message',
        'name' => '',
        'type' => 'message',
        'message' => '',
        'new_lines' => 'wpautop',
        'esc_html' => 0,
    ];

    public function content($message)
    {
        $this->message = $message;

        return $this;
    }
}
