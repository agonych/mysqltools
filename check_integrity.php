<?php

require_once 'settings.php';

header("Content-Type: text/html; charset=UTF-8");

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die('<p>Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>');
}

echo "<h2>Database Integrity and Configuration Check</h2>";

// Display the database default charset and collation
$charsetRes = $conn->query("SELECT @@character_set_database AS charset, @@collation_database AS collation");
if ($charsetRes) {
    $charset = $charsetRes->fetch_assoc();
    echo "<p><strong>Database Default Encoding:</strong> Charset - " . $charset['charset'] . ", Collation - " . $charset['collation'] . "</p>";
} else {
    echo "<p>Error fetching database charset and collation.</p>";
}

// Fetch and display all tables with row counts and their specific charset and collation
$tableRes = $conn->query("SELECT TABLE_NAME, TABLE_COLLATION, TABLE_ROWS FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "'");
if ($tableRes) {
    echo "<h3>Tables Detail</h3>";
    echo "<ul>";
    while ($table = $tableRes->fetch_assoc()) {
        // Extract charset from collation
        $collation = $table['TABLE_COLLATION'];
        $charset = explode('_', $collation)[0];

        echo "<li><strong>" . $table['TABLE_NAME'] . ":</strong> " . $table['TABLE_ROWS'] . " rows, Charset: " . $charset . ", Collation: " . $collation . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Error fetching table details.</p>";
}

// Simple database health check by querying each table
echo "<h3>Database Health Check</h3>";
if ($tableRes) {
    $tableRes->data_seek(0); // Reset the result set to re-use it
    while ($table = $tableRes->fetch_assoc()) {
        $checkRes = $conn->query("CHECK TABLE `" . $table['TABLE_NAME'] . "`");
        if ($check = $checkRes->fetch_assoc()) {
            echo "<p><strong>" . $table['TABLE_NAME'] . ":</strong> " . $check['Msg_text'] . "</p>";
        } else {
            echo "<p>Error checking table " . $table['TABLE_NAME'] . ".</p>";
        }
    }
} else {
    echo "<p>Error performing health check on tables.</p>";
}

$conn->close();