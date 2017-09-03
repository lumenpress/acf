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

    /**
     * [$postClass description]
     * @var [type]
     */
    protected $postClass = Post::class;

    /**
     * [$termClass description]
     * @var [type]
     */
    protected $termClass = Term::class;
}
