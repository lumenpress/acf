<?php 

namespace Lumenpress\ACF\Tests;

use Lumenpress\ACF\Schema;
use Lumenpress\ACF\Fields\Field;
use Lumenpress\ACF\Models\Post;
use Lumenpress\ACF\Models\FieldGroup;
use Lumenpress\ORM\Collections\RelatedCollection;

class PostTest extends TestCase
{
    /**
     * @group post
     */
    public function testCreatingSchema()
    {
        $result = Schema::create('post_fields', function($group) {
            $group->title('Post Fields');
            $group->location('post_type', 'post');
            $group->fields(function($field) {
                // $field->dropAll();
                $field->text('text');
                $field->textarea('textarea');
                $field->number('number')->label('Number');
                $field->email('email')->label('Email');
                $field->url('url')->label('URL');
                $field->password('password')->label('Password');

                $field->wysiwyg('wysiwyg');
                $field->oembed('oembed');
                $field->image('image');
                $field->file('file');
                $field->gallery('gallery');

                $field->true_false('true_false');
                $field->checkbox('checkbox')->choices(['checkbox1', 'checkbox2']);
                $field->radio('radio')->choices(['radio1', 'radio2']);
                $field->select('select')->choices(['select1', 'select2']);

                // Relational
                $field->link('link');
                $field->page_link('page_link');
                $field->post_object('post_object');
                $field->relationship('relationship');
                $field->taxonomy('taxonomy');
                $field->user('user');

                // JQuery
                $field->google_map('google_map');
                $field->date_picker('date_picker');
                $field->date_time_picker('date_time_picker');
                $field->time_picker('time_picker');
                $field->color_picker('color_picker');
                // $field->message('Message')->content('Content');
                // $field->tab('tab');
                $field->group('group')->fields(function($field) {
                    $field->text('text');
                    $field->image('image');
                });
                $field->repeater('repeater')->fields(function($field) {
                    $field->text('text')->label('Text');
                    $field->image('image');
                });
                $field->flexible('flexible')->layouts(function($flexible) {
                    $flexible->layout('layout1')->label('Layout 1')->fields(function($field) {
                        $field->textarea('textarea');
                    });
                    $flexible->layout('layout2')->label('Layout 2')->fields(function($field) {
                        $field->text('text');
                    });
                });
                // $field->clone('clone')->fields('group_1b8797f52e1e7731');
                // $field->drop('text', 'textarea');
                // $field->rename('text', 'text2');
            });
        });
        $this->assertTrue($result);
    }

    /**
     * @group post
     */
    public function testPostFields()
    {
        $post = new Post;

        $post->title = 'test post fields';

        foreach ($post->acf as $key => $value) {
            $this->assertTrue(!isset($post->acf->$key));
        }

        // $file = lumenpress_asset_url('assets/LUMENPRESS.svg');

        $data = [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'number' => 2,
            'email' => 'hello@example.com',
            'url' => 'http://example.com',
            'password' => '123456',
            // 'file' => $file,
            // 'image' => $file,
            'true_false' => true,
            'group' => [
                'text' => 'Text',
                // 'image' => $file,
            ],
            'repeater' => [
                [
                    'text' => 'Text1',
                    // 'image' => $file,
                ],
                [
                    'text' => 'Text2',
                    // 'image' => $file,
                ],
            ],
            'flexible' => [
                [
                    '_layout' => 'layout1',
                    'textarea' => 'Text1',
                ],
                [
                    '_layout' => 'layout2',
                    'text' => 'Text2',
                ],
            ]
        ];

        foreach ($data as $key => $value) {
            // setter
            $post->acf->$key = $value;
            // isset
            $this->assertTrue(isset($post->acf->$key));
        }

        // getter
        foreach ($data['group'] as $key => $value) {
            if (!is_array($value)) {
                $this->assertTrue($post->acf->group[$key]->value == $value);
                $this->assertTrue((string)$post->acf->group[$key] == $value);
            }
        }

        foreach ($data['repeater'] as $index => $item) {
            foreach ($item as $key => $value) {
                $this->assertTrue($post->acf->repeater[$index][$key]->value == $value);
            }
        }

        foreach ($post->acf->repeater as $index => $item) {
            foreach ($item as $key => $field) {
                if ($data['repeater'][$index][$key]) {
                    $this->assertTrue($data['repeater'][$index][$key] == $field->value);
                }
            }
        }

        foreach ($data['flexible'] as $index => $item) {
            foreach ($item as $key => $value) {
                $this->assertTrue((string)$post->acf->flexible[$index][$key] == $value);
            }
        }

        foreach ($post->acf->flexible as $index => $item) {
            foreach ($item as $key => $field) {
                if ($data['flexible'][$index][$key]) {
                    $this->assertTrue($data['flexible'][$index][$key] == (string) $field);
                }
            }
        }

        $post->save();
    }
}
