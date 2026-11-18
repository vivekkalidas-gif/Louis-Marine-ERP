<?php
$file = __DIR__ . '/../resources/views/admin/staff/show.blade.php';
$content = file_get_contents($file);

echo "<h3>Check for 'ledger.store' usage:</h3>";
if (strpos($content, 'ledger.store') !== false) {
    echo "<p style='color:red'>FOUND route('...ledger.store') in file!</p>";
} else {
    echo "<p style='color:green'>NOT FOUND. Good.</p>";
}

echo "<h3>Check for 'fix_view.php' hardcoded URL:</h3>";
if (strpos($content, 'action="/admin/staff/') !== false) {
    echo "<p style='color:green'>FOUND hardcoded action URL.</p>";
} else {
    echo "<p style='color:red'>NOT FOUND hardcoded URL.</p>";
}

echo "<h3>First 500 chars of Advance Modal form:</h3>";
$pos = strpos($content, 'advanceModal');
if ($pos !== false) {
    echo "<pre>" . htmlspecialchars(substr($content, $pos, 800)) . "</pre>";
}
