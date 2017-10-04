<?php

namespace LumenPress\ACF\Tests\Models;

use LumenPress\ACF\Concerns\HasFields;

class Option extends \LumenPress\Nimble\Models\Option
{
    use HasFields;

    public function getAttribute($key)
    {
        if (is_null($value = parent::getAttribute($key))) {
            if (isset($this->acf->$key)) {
                return $this->acf->$key;
            }
        }

        return $value;
    }
}
