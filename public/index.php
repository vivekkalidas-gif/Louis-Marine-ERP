<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Case-Insensitivity Fix for XAMPP
if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['REQUEST_URI'])) {
    if (stripos($_SERVER['SCRIPT_NAME'], '/Louis/') !== false && stripos($_SERVER['REQUEST_URI'], '/louis/') !== false) {
        $_SERVER['SCRIPT_NAME'] = str_ireplace('/Louis/', '/louis/', $_SERVER['SCRIPT_NAME']);
    }
}

$app->handleRequest(Request::capture());
