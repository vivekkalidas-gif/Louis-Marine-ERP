<?php
$file = __DIR__ . '/../resources/views/admin/staff/show.blade.php';
$content = file_get_contents($file);

// Replace all $user with $staff
$content = str_replace('$user', '$staff', $content);

file_put_contents($file, $content);
echo "View updated: \$user → \$staff at " . date('H:i:s');
