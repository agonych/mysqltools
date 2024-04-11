<?php

require_once 'settings.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!file_exists(DUMP_DIR) && !mkdir(DUMP_DIR, 0777, true)) {
        die('Failed to create dump folder...');
    }

    if (isset($_FILES['zip_file'])) {
        $zipFilePath = $_FILES['zip_file']['tmp_name'];
        $zipFileName = $_FILES['zip_file']['name'];

        if ($_FILES['zip_file']['error'] === UPLOAD_ERR_OK) {
            $zip = new ZipArchive;
            if ($zip->open($zipFilePath) === TRUE) {
                $zip->extractTo(DUMP_DIR);
                $zip->close();
                echo "<p>File '$zipFileName' uploaded and extracted successfully.</p>";
            } else {
                echo "<p>Failed to open uploaded ZIP file.</p>";
            }
        } else {
            echo "<p>Error uploading file: " . $_FILES['zip_file']['error'] . "</p>";
        }
    } else {
        echo "<p>No file uploaded.</p>";
    }
} else {
    displayForm();
}

function displayForm() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload ZIP File</title>
</head>
<body>
    <h2>Upload ZIP File</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="zip_file" required>
        <button type="submit">Upload and Extract</button>
    </form>
</body>
</html>
HTML;
}
