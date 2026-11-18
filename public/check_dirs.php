<?php
$path = __DIR__ . '/../resources/views/admin';
$dirs = scandir($path);
echo "<h3>Listing: $path</h3>";
foreach ($dirs as $dir) {
    if ($dir === '.' || $dir === '..') continue;
    echo $dir . " (" . (is_dir("$path/$dir") ? 'DIR' : 'FILE') . ")<br>";
}
