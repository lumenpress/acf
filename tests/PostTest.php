<?php 

namespace Lumenpress\Acf\Tests;

use Lumenpress\ORM\Schema;
use Lumenpress\Acf\Models\FieldGroup;
use Lumenpress\Acf\Fields\Field;
use Lumenpress\Acf\Tests\models\Post;

class PostTest extends TestCase
{
    public function testPost()
    {
        $post = new Post;
        $post->title = 'test acf fields';
        $this->assertTrue($post->save());
    }
}
