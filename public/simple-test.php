<?php
// Create a test image file to verify directory works
$testDir = __DIR__ . '/uploads/posts/';
$testFile = $testDir . 'simple-test-' . time() . '.txt';

echo '<h2>Simple Directory Test</h2>';
echo '<p>Test Directory: ' . $testDir . '</p>';
echo '<p>Directory exists: ' . (is_dir($testDir) ? 'YES' : 'NO') . '</p>';
echo '<p>Directory writable: ' . (is_writable($testDir) ? 'YES' : 'NO') . '</p>';

// Try to create a simple file
$content = 'Test content ' . date('Y-m-d H:i:s');
if (file_put_contents($testFile, $content)) {
    echo '<p style="color: green;">SUCCESS: File created</p>';
    echo '<p>File: ' . $testFile . '</p>';
    echo '<p>File exists: ' . (file_exists($testFile) ? 'YES' : 'NO') . '</p>';
    echo '<p>File size: ' . filesize($testFile) . ' bytes</p>';
} else {
    echo '<p style="color: red;">FAILED: Could not create file</p>';
}

// List all files in directory
echo '<h3>Files in directory:</h3>';
$files = scandir($testDir);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo '<p>' . $file . ' (' . filesize($testDir . $file) . ' bytes)</p>';
    }
}
?>
