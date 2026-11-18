<?php
$file = __DIR__ . '/../resources/views/admin/staff/show.blade.php';
$content = file_get_contents($file);

// Remove the DEBUG section (between the first <div> after x-app-layout and before "Employee Details")
$pattern = '/<div[^>]*>\s*DEBUG:.*?<\/div>/s';
$content = preg_replace($pattern, '', $content);

// Also remove any standalone DEBUG: paragraphs or divs
$content = preg_replace('/\s*<[^>]*>DEBUG:.*?<\/[^>]+>\s*/s', '', $content);

file_put_contents($file, $content);

echo "<p style='color:green'>✓ Debug information removed from staff profile view</p>";
echo "<p>Run: <a href='/clear_views_now.php'>clear_views_now.php</a> to clear cache</p>";
