<?php 

namespace Lumenpress\Acf\Collections;

use Lumenpress\Acf\Fields\Field;
use Lumenpress\Acf\Fields\FlexibleLayout;
use Lumenpress\ORM\Collections\AbstractCollection;

class LayoutCollection extends AbstractCollection
{
    public function layout($name)
    {
        foreach ($this->items as $index => $item) {
            if ($item->name == $name) {
                return $item;
            }
        }
        $item = new FlexibleLayout;
        $item->key = uniqid();
        $item->name = $name;
        $item->label = $name;
        $item->setRelatedParent($this->relatedParent);
        return $this->items[] = $item;;
    }

    public function save()
    {
        foreach ($this->items as $item) {
            $item->save();
        }
    }
}
