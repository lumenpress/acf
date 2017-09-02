<?php 

namespace Lumenpress\ACF\Models;

use Lumenpress\ACF\Concerns\HasAdvancedCustomFields;

class Page extends Post
{
    protected $postType = 'page';
}
