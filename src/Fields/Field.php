<?php 

namespace Lumenpress\Acf\Fields;

use Lumenpress\ORM\Concerns\RegisterTypes;
use Lumenpress\Acf\Models\AbstractField;

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

        $class = 'Lumenpress\\Acf\\Fields\\'.studly_case(str_replace('_', ' ', $type));

        return class_exists($class) ? $class : $default;
    }
}
