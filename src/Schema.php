<?php 

namespace Lumenpress\Acf;

use Lumenpress\Acf\Models\FieldGroup;

class Schema
{
    public static function create($key, callable $callable)
    {
        $key = static::hashKey($key);
        $group = FieldGroup::where('post_name', $key)->first();

        if ($group) {
            throw new \Exception("The \"$key\" field group already exists.", 1);
        }

        $group = new FieldGroup;
        $group->key = $key;
        $callable($group);

        return $group;
    }

    public static function createIfNotExist($key, callable $callable)
    {
        $key = static::hashKey($key);
        $group = FieldGroup::where('post_name', $key)->first();

        if ($group) {
            return false;
        }

        $group = new FieldGroup;
        $group->key = $key;
        $callable($group);
        $group->save();

        return $group;
    }

    public static function group($key, callable $callable)
    {
        $group = FieldGroup::where('post_name', static::hashKey($key))->first();

        if (!$group) {
            throw new \Exception("\"$key\" field group does not exist.", 1);
        }

        $group->LocationIsBeingUpdated = true;
        $callable($group);
        $group->save();

        return $group;
    }

    public static function drop($key)
    {
        return FieldGroup::where('post_name', static::hashKey($key))->delete();
    }

    protected static function hashKey($key)
    {
        if (stripos($key, 'group_') !== 0) {
            $key = 'group_'.substr(hash('md5', $key), 8, 16);
        }
        return $key;
    }
}
