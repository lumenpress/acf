<?php 

namespace LumenPress\ACF\Tests;

use LumenPress\ACF\Schema;
// use LumenPress\Nimble\Models\Post as PostModel;
// use LumenPress\Nimble\Models\Option as OptionModel;
// use LumenPress\Nimble\Models\Meta;
// use Illuminate\Database\Eloquent\Relations\Relation;
// use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Builder;
// use LumenPress\ACF\Relations\HasACF;
// use LumenPress\ACF\Models\FieldMeta;
use LumenPress\ACF\Tests\Models\Post;
use LumenPress\ACF\Tests\Models\Option;
use Illuminate\Database\Capsule\Manager as DB;

class OptionTest extends TestCase
{
    /**
     * @group option
     */
    public function testOption()
    {
        // require realpath(dirname(PHPUNIT_COMPOSER_INSTALL).'/lumenpress/testing').'/tests/wp-tests-load.php';

        Schema::create('site_settings', function($group) {
            $group->title('Site Settings');
            $group->location('options_page', 'theme-settings');
            $group->fields(function($field) {
                $field->image('favicon');
                $field->textarea('tracking_code');
                $field->wysiwyg('footer_text');
            });
        });

        DB::table('options')->insert([
            'option_name' => 'options_footer_text',
            'option_value' => '<p>abc</p>',
        ]);

        $option = Option::all();

        $this->assertEquals('<p>abc</p>', $option->footer_text);
    }
}
