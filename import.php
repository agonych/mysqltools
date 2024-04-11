<?php

require_once 'settings.php';

header("Content-Type: text/html; charset=UTF-8");

if (!file_exists(DUMP_DIR)) {
    die('<p>Dump folder does not exist. Please check the folder path.</p>');
}

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die('<p>Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>');
}

// Retrieve the database character set
$charsetRes = $conn->query("SELECT @@character_set_database AS charset");
if ($charsetRes === false) {
    die('<p>Error fetching character set: ' . htmlspecialchars($conn->error) . '</p>');
}
$charset = $charsetRes->fetch_assoc()['charset'];
$conn->set_charset($charset);

$files = array_diff(scandir(DUMP_DIR), array('..', '.'));
$totalFiles = count($files);
$currentIndex = 1;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
        $filePath = DUMP_DIR . '/' . $file;
        echo "<p><b>Processing $file [$currentIndex/$totalFiles]...</b></p>";
        flush();

        $commands = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($commands === false) {
            echo "<p>Error reading file $file</p>";
            continue;
        }

        $commandCount = count($commands);
        $currentCommandIndex = 1;
        foreach ($commands as $command) {
            if (!empty(trim($command))) { // Avoid empty lines
                if (!$conn->query($command)) {
                    echo "<p>Error executing command at line $currentCommandIndex in $file: " . htmlspecialchars($conn->error) . "</p>";
                    flush();
                    break; // Stop executing further commands after the first error
                }
                if ($currentCommandIndex % 100 == 0 || $currentCommandIndex === $commandCount) { // Progress update or final command
                    echo ". ";
                    flush();
                }
                $currentCommandIndex++;
            }
        }

        echo "<p>Completed processing $file. Total commands: $commandCount.</p>";
        flush();
        $currentIndex++;
    }
}

$conn->close();
echo "<p><b>All files uploaded successfully.</b></p>";