<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Route;

echo "<pre>";
echo "<h3>Checking Routes for 'staff'</h3>";
$routes = Route::getRoutes();

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'staff') || str_contains($route->getName() ?? '', 'staff')) {
        echo "URI: " . $route->uri() . " | Name: " . ($route->getName() ?? 'NULL') . "\n";
    }
}
echo "</pre>";
