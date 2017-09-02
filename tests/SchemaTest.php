<?php 

namespace Lumenpress\Acf\Tests;

use Lumenpress\Acf\Schema;
use Lumenpress\Acf\Models\FieldGroup;
use Lumenpress\Acf\Fields\Field;

class SchemaTest extends TestCase
{
    public function testCreating()
    {
        $result = Schema::create('home', function($group) {
            $group->title('Home Page');
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

    public function testFieldTypes()
    {
        $types = [
            'text',
            'textarea',
            'number',
            'email',
            'url',
            'password',
            'wysiwyg',
            'oembed',
            'image',
            'file',
            'gallery',
            'true_false',
            'checkbox',
            'radio',
            'select',
            'link',
            'page_link',
            'post_object',
            'relationship',
            'taxonomy',
            'user',
            'google_map',
            'date_picker',
            'date_time_picker',
            'time_picker',
            'color_picker',
            'group',
            'repeater',
            'flexible_content',
        ];

        $group = FieldGroup::findByKey('home');

        foreach ($types as $index => $type) {
            $field = $group->fields[$index];
            $this->assertInstanceOf(Field::getClassNameByType($type), $field, $type);
            $this->assertEquals($field->type, $type);
        }

        $this->assertInstanceOf(FieldGroup::class, $group);
    }

    public function testUpdating()
    {
        $result = Schema::group('home', function($group) {
            $group->title('Home Page2');
            $group->location('post_type', 'page');
            $this->assertEquals($group->title, 'Home Page2');
            $this->assertEquals($group->location, [[[
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'page'
            ]]]);
            $this->assertInstanceOf(FieldGroup::class, $group);
        });
        $this->assertTrue($result);
    }

    public function testDrop()
    {
        Schema::drop('home');
        $this->assertNull(FieldGroup::findByKey('home'));
    }

    public function testLoaction()
    {
        Schema::create('test_location', function($group) {
            $group->title('test location');
            $group->location('post_type', 'page');
            $this->assertEquals($group->title, 'test location');
            $this->assertEquals($group->location, [[[
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'page'
            ]]]);
        });
    }

    public function testLoactionAnB()
    {
        Schema::group('test_location', function($group) {
            $group->location(
                ['post_type', 'page'], 
                ['page_template', 'home']
            );
            $this->assertEquals($group->location, [[
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page'
                ],
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'home'
                ]
            ]]);
        });
    }

    public function testLoactionAnB2()
    {
        Schema::group('test_location', function($group) {
            $group->location('post_type', 'page')
                ->location('page_template', 'home'); // AND
            $this->assertEquals($group->location, [[
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page'
                ],
                [
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'home'
                ]
            ]]);
        });
    }

    public function testLoactionAorB()
    {
        Schema::group('test_location', function($group) {
            $group->location('post_type', 'page')->orLocation('post_type', 'post'); // AND
            $this->assertEquals($group->location, [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page'
                    ]
                ],[
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post'
                    ]
                ]
            ]);
        });
    }

    public function testLoactionABorCD()
    {
        Schema::group('test_location', function($group) {
            $group->location('post_type', 'page')
                ->location('page_template', 'home')
                ->orLocation('post_type', 'post')
                ->location('post_template', 'home');
            $this->assertEquals($group->location, [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page'
                    ],
                    [
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'home'
                    ]
                ],[
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post'
                    ],
                    [
                        'param' => 'post_template',
                        'operator' => '==',
                        'value' => 'home'
                    ]
                ]
            ]);
        });
    }

    public function testLoactionABorCD2()
    {
        Schema::group('test_location', function($group) {
            $group->location(
                ['post_type', 'page'], 
                ['page_template', 'home']
            )->orLocation(
                ['post_type', 'post'],
                ['post_template', 'home']
            );
            $this->assertEquals($group->location, [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page'
                    ],
                    [
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => 'home'
                    ]
                ],[
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post'
                    ],
                    [
                        'param' => 'post_template',
                        'operator' => '==',
                        'value' => 'home'
                    ]
                ]
            ]);
        });
    }
}
