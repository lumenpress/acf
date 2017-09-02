<?php 

namespace Lumenpress\ACF\Models;

use Lumenpress\ACF\Concerns\HasACF;

class Post extends \Lumenpress\ORM\Models\Post
{
    use HasACF;

    protected static $registeredTypes = [
        'post' => Post::class,
        'page' => Page::class
    ];

    protected $taxonomyClass = PostTaxonomy::class;
}
