<?php 

namespace Lumenpress\ACF\Fields;

use Illuminate\Support\Str;
use Lumenpress\ORM\Concerns\RegisterTypes;
use Lumenpress\ACF\Models\AbstractField;

class Field extends AbstractField
{
    use RegisterTypes;

    /**
     * [$types description]
     * @var array
     */
    protected static $registeredTypes = [
        'clone' => CloneField::class,
        'flexible' => FlexibleContent::class,
    ];

    public static function getClassNameByType($type, $default = null)
    {
        if (isset(static::$registeredTypes[$type])) {
            return static::$registeredTypes[$type];
        }

        $class = 'Lumenpress\\ACF\\Fields\\'.Str::studly(str_replace('_', ' ', $type));

        return class_exists($class) ? $class : $default;
    }
}
