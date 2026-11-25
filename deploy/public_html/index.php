<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auf Hetzner Webhosting ist ~/public_html ein Symlink auf /usr/www/users/<user>.
// __DIR__ zeigt daher auf /usr/www/users/<user>, während ~/laravel unter /usr/home/<user>/laravel liegt.
if (str_starts_with(__DIR__, '/usr/www/users/')) {
    $account = explode('/', substr(__DIR__, strlen('/usr/www/users/')))[0];
    $laravelPath = '/usr/home/'.$account.'/laravel';
} else {
    $laravelPath = __DIR__.'/../laravel';
}

if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $laravelPath.'/bootstrap/app.php';

// Wichtig für @vite() und public_path(): DocumentRoot ist public_html (/usr/www/users/<user>)
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
