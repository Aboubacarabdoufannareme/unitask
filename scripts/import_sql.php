<?php
/**
 * Simple SQL import script for local development.
 * Usage:
 *  - Edit credentials below if necessary
 *  - Run from browser (http://localhost/.../scripts/import_sql.php) OR
 *    run via CLI: `php scripts\import_sql.php`
 * WARNING: Delete this file after use on a public server.
 */

// Config - change if your MySQL user/password differ
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'webtech_20205A_fannareme_abdou';
$sqlFile = __DIR__ . '/../tm2027_new.sql';

// Basic safety: only allow local CLI or localhost web requests
if (php_sapi_name() !== 'cli') {
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($remote !== '127.0.0.1' && $remote !== '::1' && $remote !== '::ffff:127.0.0.1') {
        http_response_code(403);
        echo "Forbidden: import only allowed from localhost.";
        exit;
    }
}

if (!file_exists($sqlFile)) {
    echo "SQL file not found: " . htmlspecialchars($sqlFile) . PHP_EOL;
    exit(1);
}

$conn = new mysqli($dbHost, $dbUser, $dbPass);
if ($conn->connect_error) {
    echo "Connect error: " . $conn->connect_error . PHP_EOL;
    exit(1);
}

// Create database if not exists
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    echo "Failed to create database: " . $conn->error . PHP_EOL;
    $conn->close();
    exit(1);
}

// Select DB
if (!$conn->select_db($dbName)) {
    echo "Failed to select database: " . $conn->error . PHP_EOL;
    $conn->close();
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    echo "Failed to read SQL file." . PHP_EOL;
    $conn->close();
    exit(1);
}

// Run multi-statement import
// Temporarily disable foreign key checks so DROP TABLE statements succeed
$combined = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS=1;";
if ($conn->multi_query($combined)) {
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    if ($conn->errno) {
        echo "Import completed with errors: (" . $conn->errno . ") " . $conn->error . PHP_EOL;
        $conn->close();
        exit(1);
    }

    echo "Import completed successfully." . PHP_EOL;
} else {
    echo "Import failed: " . $conn->error . PHP_EOL;
    $conn->close();
    exit(1);
}

$conn->close();
echo PHP_SAPI === 'cli' ? "Done\n" : "<p>Done</p>";
