<?php 

namespace Lumenpress\Acf\Builders;

use Illuminate\Database\Eloquent\Builder;

class FieldGroupBuilder extends Builder
{
    public function key($key)
    {
        return $this->where('post_name', $this->getHashKey($key));
    }

    public function findByKey($key)
    {
        return $this->key($key)->first();
    }

    public function getHashKey($key)
    {
        if (stripos($key, 'group_') !== 0) {
            $key = 'group_'.substr(hash('md5', $key), 8, 16);
        }
        return $key;
    }
}
