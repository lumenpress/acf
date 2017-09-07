<?php


use Illuminate\Support\Str;
use Lumenpress\Fluid\Models\Post;
use Lumenpress\Fluid\Models\Taxonomy;
use Illuminate\Database\Capsule\Manager as Capsule;

$loader = require __DIR__.'/../vendor/autoload.php';
$psr4 = require __DIR__.'/../vendor/composer/autoload_psr4.php';
$path = dirname($psr4['Lumenpress\\Fluid\\'][0]);
$loader->addPsr4('Lumenpress\\Fluid\\Tests\\', $path.'/tests');

try {
    (new Dotenv\Dotenv(__DIR__.'/../../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

date_default_timezone_set(getenv('APP_TIMEZONE'));

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER') ?: 'mysql',
    'host'      => getenv('DB_HOST') ?: 'mysql',
    'database'  => getenv('DB_NAME') ?: 'wordpress',
    'username'  => getenv('DB_USER') ?: 'wordpress',
    'password'  => getenv('DB_PASSWORD') ?: 'wordpress',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => getenv('DB_PREFIX') ?: 'wp_testing_',
]);

// Set the event dispatcher used by Eloquent models... (optional)
// use Illuminate\Events\Dispatcher;
// use Illuminate\Container\Container;

// $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

foreach (glob($path.'/tests/database/migrations/*.php') as $file) {
    require_once $file;
    $class = Str::studly(substr(basename($file, '.php'), 18));
    $class = "Lumenpress\Fluid\Tests\database\migrations\\".$class;
    $migration = new $class;
    $migration->down();
    $migration->up();
}

Post::register('post', Lumenpress\ACF\Tests\Models\Post::class);
Post::register('page', Lumenpress\ACF\Tests\Models\Page::class);

Taxonomy::setTermClass(Lumenpress\ACF\Tests\Models\Term::class);
