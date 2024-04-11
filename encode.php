<?php

require_once 'settings.php';

header("Content-Type: text/html; charset=UTF-8");

if (!file_exists(DUMP_DIR)) {
    die('<p>Dump folder does not exist. Please check the folder path.</p>');
}

if (!file_exists(ENCODED_DIR) && !mkdir(ENCODED_DIR, 0777, true)) {
    die('<p>Failed to create encoded files folder.</p>');
}

$files = array_diff(scandir(DUMP_DIR), array('..', '.'));
$totalFiles = count($files);
$currentIndex = 1;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
        $filePath = DUMP_DIR . '/' . $file;
        $newFilePath = ENCODED_DIR . '/' . $file;

        echo "<p><b>Encoding $file [$currentIndex/$totalFiles]...</b></p>";
        flush();

        $content = file_get_contents($filePath);
        if ($content === false) {
            echo "<p>Error reading file $file</p>";
            continue;
        }

        $encodedContent = mb_convert_encoding($content, TARGET_ENCODING, ORIGINAL_ENCODING);
        if (file_put_contents($newFilePath, $encodedContent) === false) {
            echo "<p>Error writing encoded file for $file</p>";
        } else {
            echo "<p>$file has been re-encoded and saved as $newFilePath.</p>";
        }

        flush();
        $currentIndex++;
    }
}

echo "<p><b>Encoding complete for all files.</b></p>";
