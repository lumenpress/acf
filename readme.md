# Advanced Custom Fields Plugin for LumenPress

- [Installation](#installation)
- [Schema](#schema)
  - [Field Groups](#field-groups)
    - [Creating Field Groups](#creating-field-groups)
    - [Renaming / Dropping Field Groups](#renaming--dropping-field-groups)
  - [Fields](#)
    - [Creating Fields](#creating-fields)
    - [Modifying Fields](#modifying-fields)
    - [Dropping Fields](#dropping-fields)
- [Models](#models)
  - [Inserts](#inserts)
  - [Updates](#updates)
  - [Deletes](#deletes)
- [Builders](#builders)
  - [Retrieving Results](#retrieving-results)
  - [Inserting Values](#inserting-values)
  - [Updating Values](#updating-values)
  - [Deleting Values](#deleting-values)

## Installation

```
composer require lumenpress/acf
```

Copy the `config/acf.php` file to your local config folder and register the configuration + Service Provider in bootstrap/app.php:

```php
$app->configure('acf'); 
$app->register(Lumenpress\Acf\ServiceProvider::class);
```

## ACF Schema

### Field Groups

#### Creating Field Groups

```php
use Lumenpress\Acf\Schema;
use Lumenpress\Acf\Models\FieldGroup;

Schema::create('field_group_1', function (FieldGroup $group) {
    $group->location('post_type', 'post');

    $group->fields(function($fields) {

        // Basic
        $fields->text('text')->label('Text');
        $fields->textarea('textarea')->label('Textarea');
        $fields->number('number')->label('Number');
        $fields->email('email')->label('Email');
        $fields->url('url')->label('URL');
        $fields->password('password')->label('Password');

        // Content
        $fields->wysiwyg('wysiwyg');
        $fields->oembed('oembed');
        $fields->image('image');
        $fields->file('file');
        $fields->gallery('gallery');

        // Choice
        $fields->true_false('true_false');
        $fields->checkbox('checkbox')->choices(['value1', 'value2']);
        $fields->radio('radio')->choices(['value1', 'value2']);;
        $fields->select('select')->choices(['value1', 'value2']);;

        // Relational
        $fields->link('link');
        $fields->page_link('page_link');
        $fields->post_object('post_object');
        $fields->relationship('relationship');
        $fields->taxonomy('taxonomy');
        $fields->user('user');

        // JQuery
        $fields->google_map('google_map');
        $fields->date_picker('date_picker');
        $fields->date_time_picker('date_time_picker');
        $fields->time_picker('time_picker');
        $fields->color_picker('color_picker');

        // Layout
        $fields->message('Message Content');
        $fields->tab('tab');
        
        $fields->group('group')->layout('block')->fields(function($fields) {
            $fields->text('text')->label('Text');
        });
        
        $fields->repeater('repeater')->layout('row')->fields(function($fields) {
            $fields->text('text')->label('Text');
            $fields->repeater('repeater')->layout('row')->fields(function($fields) {
                $fields->text('text')->label('Text');
            });
        });
        
        $fields->flexible('flexible')->layouts(function($flexible) {
            $flexible->layout('layout1')->label('Layout 1')->fields(function($fields) {
                $fields->textarea('textarea')->label('Textarea');
            });
            $flexible->layout('layout2')->label('Layout 2')->fields(function($fields) {
                $fields->text('text')->label('Text');
            });
        });

        $fields->clone(['field_group_1.text']);
    });
});
```

#### Renaming / Dropping Field Groups

To rename an existing field group, use the `rename` method:

```php
FieldGroup::rename($from, $to);
```

To drop an existing field group, you may use the `drop` methods:

```php
FieldGroup::drop('field_group_1');
```

### Fields

#### Creating Fields

```php
Schema::create('field_group_1', function (FieldGroup $group) {
    $group->location('post_type', 'post');

    $group->fields(function($fields) {
        $fields->text('text')->label('Text');
    });
});
```

Available Field Types

```php
// Basic
$fields->text('text')->label('Text');
$fields->textarea('textarea')->label('Textarea');
$fields->number('number')->label('Number');
$fields->email('email')->label('Email');
$fields->url('url')->label('URL');
$fields->password('password')->label('Password');

// Content
$fields->wysiwyg('wysiwyg');
$fields->oembed('oembed');
$fields->image('image');
$fields->file('file');
$fields->gallery('gallery');

// Choice
$fields->true_false('true_false');
$fields->checkbox('checkbox')->choices(['value1', 'value2']);
$fields->radio('radio')->choices(['value1', 'value2']);;
$fields->select('select')->choices(['value1', 'value2']);;

// Relational
$fields->page_link('page_link');
$fields->post_object('post_object');
$fields->relationship('relationship');
$fields->taxonomy('taxonomy');
$fields->user('user');

// JQuery
$fields->google_map('google_map');
$fields->date_picker('date_picker');
$fields->date_time_picker('date_time_picker');
$fields->time_picker('time_picker');
$fields->color_picker('color_picker');

// Layout
$fields->message('Message Content');
$fields->tab('tab');
$fields->repeater('repeater')->layout('row')->fields(function($fields) {
    $fields->text('text')->label('Text');
    $fields->repeater('repeater')->layout('row')->fields(function($fields) {
        $fields->text('text')->label('Text');
    });
});
$fields->flexible('flexible')->layouts(function($flexible) {
    $flexible->layout('layout1')->label('Layout 1')->fields(function($fields) {
        $fields->textarea('textarea')->label('Textarea');
    });
    $flexible->layout('layout2')->label('Layout 2')->fields(function($fields) {
        $fields->text('text')->label('Text');
    });
});
$fields->clone(['field_group_1.text']);
```

#### Modifying Fields

Updating Field Attributes

```php
Schema::table('field_group_1', function (FieldGroup $group) {
    $group->fields(function($fields) {
        $fields->text('text')->label('Text2');
    });
});
```

Renaming Fields

```php
Schema::table('field_group_1', function (FieldGroup $group) {
    $group->fields(function($fields) {
        $fields->rename('from', 'to');
    });
});
```

#### Dropping Fields

```php
Schema::table('field_group_1', function (FieldGroup $group) {
    $group->fields(function($fields) {
        // single field
        $fields->drop('text');
        // multiple fields
        $fields->drop(['text', 'textarea']);
    });
});
```

## Models

```php
class Post extends \Lumenpress\ORM\Models\Post
{
    use HasAdvancedCustomFields;
}
```

### Inserts

```php
$post = new Post;

$post->title = 'Title1';
$post->type = 'post';

$post->acf->schema('home_fields');

$post->acf->text = 'Text';
$post->acf->textarea = 'Textarea';

$post->save();
```

### Updates

```php
$post = Post::find(2);

$post->acf->text = 'Text2';
$post->acf->textarea = 'Textarea2';

$post->save();
```

### Deletes

```php
$post = Post::find(2);

unset($post->acf->text);
unset($post->acf->textarea);

$post->save();
```

## Builders

```php
$post = Post::find(2);
$builder = $post->acf();
```

### Retrieving Results

```php
$post->acf('abc')->value();
$post->acf()->text('abc')->value();
```

### Inserting Values

```php
$post->acf()->text('abc')->insertValue('Value 1');
```

### Updating Values

```php
$post->acf()->text('abc')->updateValue('Value 2');
```

### Deleting Values

```php
$post->acf('abc')->delete();
$post->acf()->text('abc')->delete();
```




