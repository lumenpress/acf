<?php 

namespace Lumenpress\ACF;

use Lumenpress\ACF\Models\FieldGroup;

class Schema
{
    public static function create($key, callable $callable)
    {
        if ($group = FieldGroup::findByKey($key)) {
            throw new \Exception("The \"$key\" field group already exists.", 1);
        }

        $group = new FieldGroup;
        $group->key = static::getHashKey($key);
        $callable($group);

        return $group->save();
    }

    public static function createIfNotExist($key, callable $callable)
    {
        if ($group = FieldGroup::findByKey($key)) {
            return false;
        }

        $group = new FieldGroup;
        $group->key = static::getHashKey($key);
        $callable($group);

        return $group->save();
    }

    public static function group($key, callable $callable)
    {
        if (!($group = FieldGroup::findByKey($key))) {
            throw new \Exception("\"$key\" field group does not exist.", 1);
        }

        $group->LocationIsBeingUpdated = true;
        $callable($group);

        return $group->save();
    }

    public static function drop($key)
    {
        return ($group = FieldGroup::findByKey($key)) ? $group->delete() : false;
    }

    public static function getHashKey($key)
    {
        if (stripos($key, 'group_') !== 0) {
            $key = 'group_'.substr(hash('md5', $key), 8, 16);
        }
        return $key;
    }
}
