<?php 

namespace Lumenpress\Acf\Collections;

use Lumenpress\Acf\Fields\Field;
use Lumenpress\ORM\Collections\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Collection;

class FieldCollection extends Collection
{
    use HasRelationships;

    protected $layoutKey;

    /**
     * [__call description]
     * @param  [type] $method     [description]
     * @param  [type] $parameters [description]
     * @return [type]             [description]
     */
    public function __call($type, $parameters)
    {
        foreach ($this->items as $key => $item) {
            if ($item->name == $parameters[0]) {
                return $item;
            }
        }
        if ($className = Field::getClassNameByType($type)) {
            $field = new $className;
            // if ($this->layoutKey) {
            //     $field->setContentAttribute('parent_layout', $this->layoutKey);
            // }
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

    public function save()
    {
        foreach ($this->items as $item) {
            $item->post_parent = $this->relatedParent->id;
            $item->save();
        }
    }
}
