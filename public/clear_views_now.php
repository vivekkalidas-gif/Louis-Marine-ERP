<?php
$files = glob(__DIR__ . '/../storage/framework/views/*.php');
$count = 0;
foreach ($files as $file) {
    if (is_file($file)) {
        @unlink($file);
        $count++;
    }
}
echo "Deleted $count view cache files at " . date('H:i:s');
