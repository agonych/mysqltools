<?php

require_once 'settings.php';

header("Content-Type: text/html; charset=UTF-8");

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die('<p>Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>');
}

echo "<h2>Table Optimization and Repair</h2>";

// Fetch all table names from the database
$result = $conn->query("SHOW TABLES");
if (!$result) {
    die('<p>Error fetching tables: ' . htmlspecialchars($conn->error) . '</p>');
}

$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    echo "<h3>Processing Table: $table</h3>";

    // Check and repair table
    $repair = $conn->query("REPAIR TABLE `$table`");
    if ($repair) {
        $msg = $repair->fetch_assoc();
        echo "<p>Repair Status: " . $msg['Msg_text'] . "</p>";
    } else {
        echo "<p>Error repairing table $table: " . htmlspecialchars($conn->error) . "</p>";
    }

    // Optimize table
    $optimize = $conn->query("OPTIMIZE TABLE `$table`");
    if ($optimize) {
        $msg = $optimize->fetch_assoc();
        echo "<p>Optimize Status: " . $msg['Msg_text'] . "</p>";
    } else {
        echo "<p>Error optimizing table $table: " . htmlspecialchars($conn->error) . "</p>";
    }
}

$conn->close();
