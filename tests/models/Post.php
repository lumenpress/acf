<?php 

namespace Lumenpress\Acf\Tests\models;

use Lumenpress\Acf\Concerns\HasAdvancedCustomFields;

class Post extends \Lumenpress\ORM\Models\Post
{
    use HasAdvancedCustomFields;
}
