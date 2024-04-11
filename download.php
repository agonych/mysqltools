<?php

require_once 'settings.php';

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=dump_files_backup.zip");
header("Pragma: no-cache");
header("Expires: 0");

$zip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'dump'); // Create a temporary file in the system's temp directory
$zip->open($tempFile, ZipArchive::CREATE);

$dir = new DirectoryIterator(DUMP_DIR);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $filePath = $fileinfo->getPathname();
        $relativePath = $fileinfo->getFilename();
        if (!$zip->addFile($filePath, $relativePath)) {
            echo "Could not add file: $relativePath";
            exit;
        }
    }
}

$zip->close();

// Stream the file to the client
readfile($tempFile);
unlink($tempFile); // Remove the temp file after download

exit;
