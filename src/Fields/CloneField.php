<?php

namespace Lumenpress\ACF\Fields;

use Lumenpress\ACF\Schema;
use Lumenpress\ACF\Models\FieldGroup;

class CloneField extends Field
{
    protected $groupKey;

    protected $defaults = [
        // 'key' => 'field_5979ac7b766e4',
        // 'label' => 'Clone',
        // 'name' => 'clone',
        'type' => 'clone',
        'clone' => [],
        'display' => 'seamless',
        'layout' => 'block',
        'prefix_label' => 0,
        'prefix_name' => 0,
    ];

    public function setGroupKey($groupKey)
    {
        $this->groupKey = $groupKey;
    }

    public function fields($fields = null)
    {
        $clone = [];
        foreach (func_get_args() as $key) {
            if (stripos($key, 'group_') === 0 || stripos($key, 'field_') === 0) {
                $clone[] = $key;
            } elseif (stripos($key, '.') !== false) {
                $keys = explode('.', $key);
                if (stripos($keys[1], 'field_') === 0) {
                    $clone[] = $keys[1];
                } else {
                    $key = Schema::getHashKey($keys[0]);
                    if ($key === $this->groupKey) {
                        $item = $this->{$keys[1]};
                    } else {
                        $group = FieldGroup::where('post_name', $key)->first();
                        $item = $group->fields->{$key[1]};
                    }
                    if ($item instanceof Field) {
                        $clone[] = $item->key;
                    }
                }
            }
        }
        $this->clone = $clone;
    }
}
