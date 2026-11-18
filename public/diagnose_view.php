<?php
$file = __DIR__ . '/../resources/views/admin/staff/show.blade.php';

echo "<h3>Diagnostic Report</h3>";
echo "File: $file<br>";
echo "Exists: " . (file_exists($file) ? 'YES' : 'NO') . "<br>";

if (file_exists($file)) {
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($file)) . "<br>";
    echo "Size: " . filesize($file) . " bytes<br><br>";
    
    $content = file_get_contents($file);
    
    // Check for hardcoded URL
    if (strpos($content, 'action="/admin/staff/') !== false) {
        echo "<p style='color:green'><strong>✓ GOOD:</strong> Hardcoded form action found</p>";
    } else {
        echo "<p style='color:red'><strong>✗ BAD:</strong> Hardcoded form action NOT found</p>";
    }
    
    // Check for route() calls
    if (strpos($content, "route('admin.staff.ledger.store'") !== false) {
        echo "<p style='color:red'><strong>✗ BAD:</strong> route() call found (should be hardcoded)</p>";
    } else {
        echo "<p style='color:green'><strong>✓ GOOD:</strong> No route() calls found</p>";
    }
    
    // Check for route() calls with any variation
    if (preg_match("/route\s*\(\s*['\"].*ledger\.store/", $content)) {
        echo "<p style='color:orange'><strong>⚠ WARNING:</strong> Some route() call related to ledger.store found</p>";
    }
    
    echo "<hr>";
    echo "<h4>File Preview (first 50 lines):</h4>";
    echo "<pre>" . htmlspecialchars(implode("\n", array_slice(explode("\n", $content), 0, 50))) . "</pre>";
}
