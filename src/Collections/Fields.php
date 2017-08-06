<?php 

namespace Lumenpress\Models\Acf\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lumenpress\Models\Acf\Fields\Field;

class Fields extends Collection
{
    protected $layoutKey;

    /**
     * [__call description]
     * @param  [type] $method     [description]
     * @param  [type] $parameters [description]
     * @return [type]             [description]
     */
    public function __call($type, $parameters)
    {
        $class = Field::getClass($type);
        if ($class !== Field::class) {
            $field = new $class;
            // $field->type = $type;
            if ($this->layoutKey) {
                $field->setContentAttribute('parent_layout', $this->layoutKey);
            }
            $field->name = str_slug($parameters[0], '_');
            $field->label = ucwords(str_replace('_', ' ', $field->name));
            return $this->items[] = $field;
        }
        return parent::__call($type, $parameters);
    }

    public function setLayoutKey($key)
    {
        $this->layoutKey = $key;
    }

    public function save(Model $parent)
    {
        foreach ($this->items as $item) {
            $item->post_parent = $parent->getKey();
            $item->save();
        }
    }
}
