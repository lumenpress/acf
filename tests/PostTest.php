<?php

namespace LumenPress\ACF\Tests;

use LumenPress\ACF\Schema;
use LumenPress\Nimble\Models\Tag;
use Illuminate\Support\Collection;
use LumenPress\Nimble\Models\Category;
use LumenPress\ACF\Tests\Models\Post;

class PostTest extends TestCase
{
    /**
     * @group post
     */
    public function testCreatingSchema()
    {
        $result = Schema::create('post_fields', function ($group) {
            $group->title('Post Fields');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
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
                $field->group('group')->fields(function ($field) {
                    $field->text('text');
                    $field->image('image');
                });
                $field->repeater('repeater')->fields(function ($field) {
                    $field->text('text')->label('Text');
                    $field->image('image');
                });
                $field->flexible('flexible')->layouts(function ($flexible) {
                    $flexible->layout('layout1')->label('Layout 1')->fields(function ($field) {
                        $field->textarea('textarea');
                    });
                    $flexible->layout('layout2')->label('Layout 2')->fields(function ($field) {
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
    public function dtestPostFields()
    {
        $post = new Post;

        $post->title = 'test post fields';

        foreach ($post->acf as $key => $value) {
            $this->assertTrue(! isset($post->acf->$key), $key);
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
            ],
        ];

        foreach ($data as $key => $value) {
            // setter
            $post->acf->$key = $value;
            // isset
            $this->assertTrue(isset($post->acf->$key));
        }

        // getter
        foreach ($data['group'] as $key => $value) {
            if (! is_array($value)) {
                $this->assertTrue($post->acf->group[$key]->value == $value);
                $this->assertTrue((string) $post->acf->group[$key] == $value);
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
                $this->assertTrue((string) $post->acf->flexible[$index][$key] == $value);
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

    /**
     * @group basic-field
     */
    public function testBasicField()
    {
        Schema::create('basic_fields', function ($group) {
            $group->title('basic fields');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
                $field->text('basic_text');
                $field->textarea('basic_textarea');
                $field->number('basic_number')->label('Number');
                $field->email('basic_email')->label('Email');
                $field->url('basic_url')->label('URL');
                $field->password('basic_password')->label('Password');
            });
        });

        $data = [
            'basic_text' => 'Text',
            'basic_textarea' => 'Textarea',
            'basic_number' => 2,
            'basic_email' => 'hello@example.com',
            'basic_url' => 'http://example.com',
            'basic_password' => '123456',
        ];

        $post = new Post;
        $post->title = 'test basic fields';

        foreach ($data as $key => $value) {
            // isset
            $this->assertTrue(! isset($post->acf->$key));
            // setter
            $post->acf->$key = $value;
            // isset
            $this->assertTrue(isset($post->acf->$key));
            // getter
            $this->assertEquals($value, $post->acf->$key, $key);
        }

        $post->save();
        $post = Post::find($post->id);

        foreach ($data as $key => $value) {
            // isset
            $this->assertTrue(isset($post->acf->$key));
            // getter
            $this->assertEquals($value, $post->acf->$key, $key);
        }
    }

    /**
     * @group choice-field
     */
    public function testChoiceFields()
    {
        Schema::create('choice_fields', function ($group) {
            $group->title('choice fields');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
                $field->true_false('choice_true_false');
                $field->checkbox('choice_checkbox')->choices(['checkbox1', 'checkbox2']);
                $field->radio('choice_radio')->choices(['radio1', 'radio2']);
                $field->select('choice_single_select')->choices(['select1', 'select2']);
                $field->select('choice_multiple_select')->choices(['select1', 'select2'])->multiple(1);
            });
        });

        $post = new Post;
        $post->title = 'test choice fields';

        $data = [
            'choice_true_false' => 1,
            'choice_radio' => 'radio1',
            'choice_checkbox' => ['checkbox1', 'checkbox2'],
            'choice_single_select' => 'select1',
            'choice_multiple_select' => ['select1', 'select2'],
        ];

        foreach ($data as $key => $value) {
            // isset
            $this->assertTrue(! isset($post->acf->$key));
            // setter
            $post->acf->$key = $value;
            // isset
            $this->assertTrue(isset($post->acf->$key));
            // getter
            $this->assertEquals($value, $post->acf->$key, $key);
        }

        $post->save();
        $post = Post::find($post->id);

        foreach ($data as $key => $value) {
            // isset
            $this->assertTrue(isset($post->acf->$key));
            // getter
            $this->assertEquals($value, $post->acf->$key, $key);
        }
    }

    /**
     * @group relational
     */
    public function testRelational()
    {
        Schema::create('relational_fields', function ($group) {
            $group->title('relational fields');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
                $field->link('relational_link');
                $field->page_link('relational_page_link');
                $field->post_object('relational_post_object');
                $field->relationship('relational_relationship');
                $field->taxonomy('relational_taxonomy');
                // $field->user('relational_user');
            });
        });

        $postObject = new Post;
        $postObject->title = 'relational post object';
        $postObject->save();

        $postObject2 = new Post;
        $postObject2->title = 'relational post object';
        $postObject2->save();

        $post = new Post;
        $post->title = 'test relational fields';

        $data = [
            'relational_link' => 'http://example.com',
            'relational_page_link' => $postObject->id,
            'relational_post_object' => $postObject2->id,
            'relational_relationship' => [$postObject->id , $postObject2->id],
        ];

        foreach ($data as $key => $value) {
            $post->acf->$key = $value;
        }

        $this->assertEquals((string) $post->acf->relational_link, 'http://example.com', 'message');
        $this->assertEquals($post->acf->relational_page_link, $postObject->link, 'message');
        $this->assertEquals($post->acf->relational_post_object->id, $postObject2->id, 'message');

        $post->save();
        $post = Post::find($post->id);

        $this->assertEquals((string) $post->acf->relational_link, 'http://example.com', 'message');
        $this->assertEquals($post->acf->relational_page_link, $postObject->link, 'message');
        $this->assertEquals($post->acf->relational_post_object->id, $postObject2->id, 'message');
    }

    /**
     * @group group-field
     */
    public function testGroupField()
    {
        Schema::create('group_field', function ($group) {
            $group->title('Group Field');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
                $field->group('group_field')->fields(function ($field) {
                    $field->text('text');
                    $field->textarea('content');
                    // $field->image('image');
                });
            });
        });

        $post = new Post;
        $post->title = 'test group field';

        $fields = [
            'text' => 'Text111111111111111', 
            'content' => 'content1111111'
        ];

        $post->acf->group_field = $fields;

        $post->save();

        $this->assertEquals($post->acf->group_field, $fields, 'message');

        $post = Post::find($post->id);

        $this->assertEquals($post->acf->group_field, $fields, 'message');
    }

    /**
     * @group repeater-field
     */
    public function testRepeaterField()
    {
        Schema::create('repeater_field', function ($group) {
            $group->title('Repeater Field');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
                $field->repeater('repeater_field')->fields(function ($field) {
                    $field->text('text');
                    $field->textarea('content');
                });
            });
        });

        $post = new Post;
        $post->title = 'test repeater field';

        $data = [
            [
                'text' => 'Text111111111111111', 
                'content' => 'content1111111'
            ],
            [
                'text' => 'Text111111111111111', 
                'content' => 'content1111111'
            ]
        ];

        $post->acf->repeater_field = $data;

        $this->assertInstanceOf(Collection::class, $post->acf->repeater_field, 'message');
        $this->assertEquals($data, $post->acf->repeater_field->all(), 'message');

        $post->save();
        $post = Post::find($post->id);

        $this->assertInstanceOf(Collection::class, $post->acf->repeater_field, 'message');
        $this->assertEquals($data, $post->acf->repeater_field->all(), 'message');
    }

    /**
     * @group flexible-field
     */
    public function testFlexibleField()
    {

        Schema::create('flex_field', function ($group) {
            $group->title('Flexible Content Field');
            $group->location('post_type', 'post');
            $group->fields(function ($field) {
                $field->flexible('flex_field')->layouts(function ($flexible) {
                    $flexible->layout('layout1')->label('Layout 1')->fields(function ($field) {
                        $field->textarea('textarea');
                    });
                    $flexible->layout('layout2')->label('Layout 2')->fields(function ($field) {
                        $field->text('text');
                    });
                });
            });
        });

        $post = new Post;
        $post->title = 'test flexible field';

        $data = [
            [
                '_layout' => 'layout1',
                'textarea' => 'Text111111111111111', 
            ],
            [
                '_layout' => 'layout2',
                'text' => 'content1111111'
            ]
        ];

        $post->acf->flex_field = $data;

        $this->assertInstanceOf(Collection::class, $post->acf->flex_field, 'message');
        $this->assertEquals($data, $post->acf->flex_field->all(), 'message');

        $post->save();
        $post = Post::find($post->id);

        $this->assertInstanceOf(Collection::class, $post->acf->flex_field, 'message');
        $this->assertEquals($data, $post->acf->flex_field->all(), 'message');
    }
}
