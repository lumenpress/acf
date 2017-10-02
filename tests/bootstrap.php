<?php
use LumenPress\Nimble\Models\Post;
use LumenPress\Nimble\Models\Taxonomy;
use Illuminate\Database\Capsule\Manager as Capsule;

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$packagePath = realpath(dirname(PHPUNIT_COMPOSER_INSTALL).'/lumenpress/testing');

system("php $packagePath/tests/includes/install.php");

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => env('DB_HOST', 'mysql'),
    'database'  => env('DB_NAME', 'wordpress'),
    'username'  => env('DB_USER', 'wordpress'),
    'password'  => env('DB_PASSWORD', ''),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => env('DB_PREFIX', 'wptests_'),
    'timezone'  => env('DB_TIMEZONE', '+00:00'),
    'strict'    => env('DB_STRICT_MODE', false),
]);

// Set the event dispatcher used by Eloquent models... (optional)
// use Illuminate\Events\Dispatcher;
// use Illuminate\Container\Container;

// $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

Post::register('post', LumenPress\ACF\Tests\Models\Post::class);
Post::register('page', LumenPress\ACF\Tests\Models\Page::class);

Taxonomy::setTermClass(LumenPress\ACF\Tests\Models\Term::class);
