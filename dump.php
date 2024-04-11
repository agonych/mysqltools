<?php

require_once 'settings.php';

header("Content-Type: text/html; charset=UTF-8");

if (!file_exists(DUMP_DIR)) {
    if (!mkdir(DUMP_DIR, 0777, true)) {
        die('<p>Failed to create dump folder...</p>');
    }
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

$result = $conn->query("SHOW TABLES");
if ($result === false) {
    die('<p>Error fetching tables: ' . htmlspecialchars($conn->error) . '</p>');
}

$tables = $result->fetch_all();
$totalTables = count($tables);
$currentIndex = 1;
foreach ($tables as $row) {
    $table = $row[0];
    $resCount = $conn->query("SELECT COUNT(*) FROM `$table`");
    $rowCount = $resCount ? $resCount->fetch_row()[0] : 'N/A';
    echo "<p><b>Dumping table $table [$currentIndex/$totalTables, $rowCount rows]...</b></p>";
    flush();

    $filePath = DUMP_DIR . '/' . $table . '.sql';
    $handle = fopen($filePath, 'w');

    if (!$handle) {
        echo "<p>Error opening file for table $table</p>";
        continue;
    }

    if (strpos($charset, 'utf8') === 0) {  // Check if charset begins with 'utf8'
        fprintf($handle, "\xEF\xBB\xBF"); // Write UTF-8 BOM
    }

    fwrite($handle, "SET NAMES $charset;\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
    fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
    $createTableResult = $conn->query("SHOW CREATE TABLE `$table`");
    $tableRow = $createTableResult->fetch_assoc();
    fwrite($handle, $tableRow['Create Table'] . ";\n\n");

    $dataResult = $conn->query("SELECT * FROM `$table`");
    $count = 0;
    while ($dataResult && $dataRow = $dataResult->fetch_assoc()) {
        $vals = array_values($dataRow);
        $insertSql = "INSERT INTO `$table` VALUES (";
        $first = true;
        foreach ($vals as $val) {
            if (!$first) $insertSql .= ", ";
            $insertSql .= "'" . $conn->real_escape_string($val) . "'";
            $first = false;
        }
        $insertSql .= ");\n";
        fwrite($handle, $insertSql);

        if (++$count % 100 == 0) {
            echo '. ';
            flush();
        }
    }

    fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($handle);
    echo "<br>";
    $currentIndex++;
}

$conn->close();
echo "<p><b>Complete!</b></p>";