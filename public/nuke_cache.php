<?php
// HARD RESET OF LARAVEL CACHES via Filesystem

$cacheDir = __DIR__ . '/../bootstrap/cache';

echo "<h2>Clearing Bootstrap Cache Files from: $cacheDir</h2>";

$files = glob($cacheDir . '/*.php');
foreach ($files as $file) {
    if (is_file($file)) {
        if (unlink($file)) {
            echo "Deleted: " . basename($file) . "<br>";
        } else {
            echo "<span style='color:red'>Failed to delete: " . basename($file) . "</span><br>";
        }
    }
}

// Also try to clear storage/framework/views just in case
$viewDir = __DIR__ . '/../storage/framework/views';
echo "<h2>Clearing View Cache...</h2>";
$views = glob($viewDir . '/*.php');
foreach ($views as $view) {
    if (is_file($view)) {
        unlink($view);
    }
}
echo "View cache cleared.<br>";

echo "<h3>DONE. Please try your page now.</h3>";
