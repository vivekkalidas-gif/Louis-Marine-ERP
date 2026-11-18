<?php
$file = __DIR__ . '/../routes/web.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$lastLines = array_slice($lines, -30);

echo "<pre>";
echo "<h3>Last 30 lines of web.php</h3>";
foreach ($lastLines as $i => $line) {
    echo htmlspecialchars($line) . "\n";
}
echo "</pre>";
