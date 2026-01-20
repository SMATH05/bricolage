<?php
// Simple upload test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $uploadDir = __DIR__ . '/uploads/posts/';
    $filename = 'test-' . time() . '.jpg';
    
    echo '<h3>Upload Test Results:</h3>';
    echo '<p>Upload Directory: ' . $uploadDir . '</p>';
    echo '<p>Directory exists: ' . (is_dir($uploadDir) ? 'Yes' : 'No') . '</p>';
    echo '<p>Directory writable: ' . (is_writable($uploadDir) ? 'Yes' : 'No') . '</p>';
    echo '<p>Original file: ' . $_FILES['test_file']['name'] . '</p>';
    echo '<p>File size: ' . $_FILES['test_file']['size'] . ' bytes</p>';
    echo '<p>Upload error: ' . $_FILES['test_file']['error'] . '</p>';
    
    if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        if (move_uploaded_file($_FILES['test_file']['tmp_name'], $uploadDir . $filename)) {
            echo '<p style="color: green;">SUCCESS: File moved to ' . $filename . '</p>';
            echo '<p>File exists: ' . (file_exists($uploadDir . $filename) ? 'Yes' : 'No') . '</p>';
        } else {
            echo '<p style="color: red;">FAILED: Could not move file</p>';
        }
    }
    
    echo '<hr><a href="">Try again</a>';
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Test</title>
</head>
<body>
    <h2>Simple Upload Test</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="test_file" accept="image/*" required>
        <button type="submit">Upload Test</button>
    </form>
</body>
</html>
