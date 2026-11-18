<?php
$file = __DIR__ . '/../app/Http/Controllers/Admin/StaffController.php';
$content = file_get_contents($file);

// Replace the show method signature
$content = preg_replace(
    '/public function show\(User \$user\)/',
    'public function show(User $staff)',
    $content
);

// Replace $user with $staff in the show method (only in show method)
$showMethod = '';
if (preg_match('/(public function show\(User \$staff\).*?)(public function \w+|}\s*$)/s', $content, $matches)) {
    $showMethodBody = $matches[1];
    // Replace $user with $staff in this method only
    $newShowMethodBody = str_replace('$user', '$staff', $showMethodBody);
    $content = str_replace($showMethodBody, $newShowMethodBody, $content);
}

// Also need to update compact() call
$content = str_replace(
    "compact('user',",
    "compact('staff',",
    $content
);

file_put_contents($file, $content);
echo "Controller updated: \$user → \$staff at " . date('H:i:s');
