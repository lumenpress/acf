<?php 

namespace Lumenpress\ACF\Tests\Models;

use Lumenpress\ACF\Concerns\HasACF;

class Page extends Post
{
    protected $postType = 'page';
}
