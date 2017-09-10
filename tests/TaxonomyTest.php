<?php

namespace LumenPress\ACF\Tests;

use LumenPress\ACF\Schema;
use LumenPress\Nimble\Models\Category;
use LumenPress\Nimble\Models\Taxonomy;

class TaxonomyTest extends TestCase
{
    /**
     * @group tax
     */
    public function testCreatingSchema()
    {
        $result = Schema::create('category_fields', function ($group) {
            $group->title('Category Fields');
            $group->location('taxonomy', 'category');
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
     * @group tax
     */
    public function testTaxonomy()
    {
        $category = new Taxonomy;
        $category->taxonomy = 'category';
        $category->name = 'test term';
        $category->meta->foo = 'bar';
        $category->acf->text = 'foo';
        $category->save();

        $category = Taxonomy::find($category->id);
        $this->assertEquals('bar', $category->meta->foo, 'message');
        $this->assertEquals('foo', $category->acf->text, 'message');
    }

    /**
     * @group tax
     */
    public function testCategory()
    {
        $category = new Category;
        $category->name = 'test category';
        $category->meta->foo = 'bar';
        $category->acf->text = 'foo';
        $category->save();

        $category = Category::find($category->id);
        $this->assertEquals('bar', $category->meta->foo, 'message');
        $this->assertEquals('foo', $category->acf->text, 'message');
    }
}
