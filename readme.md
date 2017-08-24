# Advanced Custom Fields Plugin for LumenPress ORM

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

Schema::create($groupKey, function (FieldGroup $group) {
    $group->title('Title'); // required
    $group->location('post_type', 'page'); // required
});
```

The `$groupKey` should be unique, usually have `group_` as a prefix.

```php
Schema::create('group_599d8daf5e131', function (FieldGroup $group) {
    $group->title('Title'); // required
    $group->location('post_type', 'page'); // required
});
```

If you do not start with `group_`, the key will be encrypted using md5.

```php
Schema::create('home_page', function (FieldGroup $group) {
    $group->title('Home'); // required
    $group->location('post_type', 'page'); // required
});

// algorithm
echo 'group_'.substr(hash('md5', 'home_page'), 8, 16);
// group_3f239af6fe3db5c0
```

Available Field Group Settings

- `$group->title('string')` required
- `$group->location($param, $operator, $value)` required
- `$group->position('normal')`
- `$group->style('default')`
- `$group->label_placement('top')`
- `$group->instruction_placement('label')`
- `$group->hide_on_screen('metabox')`
- `$group->description('string')`
- `$group->order(0)`
- `$group->active(true)` `true` or `false`

**Location**

```php
$group->location($param, $value); // operator is '=';
$group->location($param, $operator, $value);
```

A and B

```php
$group->location(
    [$param, $operator, $value], // A
    [$param, $operator, $value]  // B
);

// another
$group->location($param, $operator, $value)  // A
    ->location($param, $operator, $value);   // B
```

A or B

```php
$group->location($param, $operator, $value)   // A
    ->orLocation($param, $operator, $value);  // B
```

(A and B) or (C and D)

```php
$group->location(
        [$param, $operator, $value],  // A
        [$param, $operator, $value]   // B
    )->orLocation(
        [$param, $operator, $value],  // C
        [$param, $operator, $value]   // D
    );

// another
$group->location($param, $operator, $value)  // A
    ->location($param, $operator, $value);   // B
    ->orLocation($param, $operator, $value)  // C
    ->location($param, $operator, $value);   // D
```

#### Renaming / Dropping Field Groups

To rename an existing field group, use the `rename` method:

```php
Schema::rename($oldKey, $newKey);
```

To drop an existing field group, you may use the `drop` methods:

```php
Schema::drop($groupKey);
```

### Fields

#### Creating Fields

```php
Schema::create($groupKey, function (FieldGroup $group) {
    $group->title('Demo'); // required
    $group->location('post_type', 'post'); // required
    $group->fields(function($field) {
        $field->text('uniqid_name')->label('Label');
    });
});
```

Available Field Types

```php
// Basic
$field->text('text')->label('Text');
$field->textarea('textarea')->label('Textarea');
$field->number('number')->label('Number');
$field->email('email')->label('Email');
$field->url('url')->label('URL');
$field->password('password')->label('Password');

// Content
$field->wysiwyg('wysiwyg');
$field->oembed('oembed');
$field->image('image');
$field->file('file');
$field->gallery('gallery');

// Choice
$field->true_false('true_false');
$field->checkbox('checkbox')->choices(['value1', 'value2']);
$field->radio('radio')->choices(['value1', 'value2']);;
$field->select('select')->choices(['value1', 'value2']);;

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

// Layout
$field->message('Message Content');
$field->tab('tab');
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
        $field->textarea('textarea')->label('Textarea');
    });
    $flexible->layout('layout2')->label('Layout 2')->fields(function($field) {
        $field->text('text')->label('Text');
    });
});
$field->clone(['group_key.field_name']);
```

#### Modifying Fields

Updating Field Attributes

```php
Schema::group($groupKey, function (FieldGroup $group) {
    $group->fields(function($field) {
        $field->text('text')->label('Text2');
    });
});
```

Renaming Fields

```php
Schema::group($groupKey, function (FieldGroup $group) {
    $group->fields(function($field) {
        $field->rename('oldname', 'newname');
    });
});
```

#### Dropping Fields

```php
Schema::group($groupKey, function (FieldGroup $group) {
    $group->fields(function($field) {
        // single field
        $field->drop('text');
        // multiple fields
        $field->drop('text', 'textarea');
        // all
        $field->dropAll();
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
