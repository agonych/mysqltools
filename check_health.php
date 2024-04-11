<?php

require_once 'settings.php';

header("Content-Type: text/html; charset=UTF-8");

echo "<h2>Server Health and Configuration Check</h2>";

// Display PHP configuration settings
echo "<h3>PHP Configuration:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Maximum Execution Time: " . ini_get('max_execution_time') . " seconds</p>";
echo "<p>Maximum Input Time: " . ini_get('max_input_time') . " seconds</p>";
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p>Upload Max Filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Post Max Size: " . ini_get('post_max_size') . "</p>";
echo "<p>Display Errors: " . ini_get('display_errors') . "</p>";
echo "<p>Error Reporting Level: " . error_reporting() . " (" . display_errors_level_tostring(error_reporting()) . ")</p>";

// Display server software and details
echo "<h3>Server Software:</h3>";
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
} else {
    echo "<p>Server software information not available.</p>";
}

// Check memory and execution limits
echo "<h3>Memory and Execution Limits Check:</h3>";
$memory_used = memory_get_usage();
$memory_limit = ini_get('memory_limit');
if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
    if ($matches[2] == 'M') {
        $memory_limit = $matches[1] * 1024 * 1024; // Convert MB to bytes
    } elseif ($matches[2] == 'G') {
        $memory_limit = $matches[1] * 1024 * 1024 * 1024; // Convert GB to bytes
    } else {
        $memory_limit = $matches[1]; // Assume bytes
    }
}
echo "<p>Memory Used: " . format_bytes($memory_used) . " / " . format_bytes($memory_limit) . " (" . ini_get('memory_limit') . ")</p>";

// Check disk space usage
echo "<h3>Disk Space Usage:</h3>";
$disk_free_space = disk_free_space("/");
$disk_total_space = disk_total_space("/");
echo "<p>Disk Free Space: " . format_bytes($disk_free_space) . " / " . format_bytes($disk_total_space) . " (Total: " . format_bytes($disk_total_space) . ")</p>";

function format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function display_errors_level_tostring($level) {
    $error_levels = array(
        E_ALL => 'E_ALL',
        E_NOTICE => 'E_NOTICE',
        E_WARNING => 'E_WARNING',
        E_ERROR => 'E_ERROR',
        E_PARSE => 'E_PARSE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED'
    );
    $strings = array();
    foreach ($error_levels as $bit => $name) {
        if (($level & $bit) == $bit) {
            $strings[] = $name;
        }
    }
    return implode(' | ', $strings);
}

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die('<p>Connection failed: ' . htmlspecialchars($conn->connect_error) . '</p>');
}

echo "<h2>MySQL Server Health and Configuration Check</h2>";

// Fetch MySQL server variables
$variables = $conn->query("SHOW VARIABLES");
echo "<h3>MySQL Configuration Variables:</h3>";
echo "<table border='1'><tr><th>Variable Name</th><th>Value</th></tr>";
while ($row = $variables->fetch_assoc()) {
    echo "<tr><td>" . htmlspecialchars($row['Variable_name']) . "</td><td>" . htmlspecialchars($row['Value']) . "</td></tr>";
}
echo "</table>";

// Fetch MySQL server status
$status = $conn->query("SHOW STATUS");
echo "<h3>MySQL Server Status:</h3>";
echo "<table border='1'><tr><th>Status Name</th><th>Value</th></tr>";
while ($row = $status->fetch_assoc()) {
    echo "<tr><td>" . htmlspecialchars($row['Variable_name']) . "</td><td>" . htmlspecialchars($row['Value']) . "</td></tr>";
}
echo "</table>";

// Perform health checks based on status variables (example: check for slow queries)
$slow_queries = $conn->query("SHOW STATUS LIKE 'Slow_queries'");
$slow_query_row = $slow_queries->fetch_assoc();

echo "<h3>Health Check Warnings:</h3>";
if ($slow_query_row['Value'] > 0) {
    echo "<p>Warning: There are " . $slow_query_row['Value'] . " slow queries. Consider optimizing your queries.</p>";
}

$conn->close();