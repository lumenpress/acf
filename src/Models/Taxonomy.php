<?php 

namespace Lumenpress\ACF\Models;

class Taxonomy extends \Lumenpress\ORM\Models\Taxonomy
{
    /**
     * [$taxonomyPost description]
     * @var array
     */
    protected static $registeredTypes = [
        'category' => Category::class,
        'post_tag' => Tag::class,
    ];

    protected $termClass = Term::class;
}
