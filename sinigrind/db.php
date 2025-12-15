<?php
// Database connection settings
$DB_HOST = "localhost";   // or "127.0.0.1"
$DB_USER = "root";        // adjust if you set a password
$DB_PASS = "";            // your MySQL root password if any
$DB_NAME = "singrind_data"; // make sure this matches your phpMyAdmin database name

// Establish global connection
$CONN = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$CONN) {
    die("Database connection failed: " . mysqli_connect_error());
}

/**
 * db_connect - Return a new mysqli connection
 * Use this when you need manual prepared statements and insert_id
 */
function db_connect() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    return $conn;
}

/**
 * runQuery - Execute SQL with optional parameters
 * Supports SELECT, INSERT, UPDATE, DELETE
 *
 * @param string $sql   The SQL query with ? placeholders
 * @param array|null $params  Array with [types, values] for prepared statement
 * @return mixed        Array of rows for SELECT, insert_id for INSERT, affected_rows for UPDATE/DELETE
 */
function runQuery($sql, $params = null) {
    global $CONN;

    $stmt = mysqli_prepare($CONN, $sql);
    if (!$stmt) {
        die("SQL prepare error: " . mysqli_error($CONN));
    }

    // Bind parameters if provided
    if ($params !== null && is_array($params) && count($params) === 2) {
        mysqli_stmt_bind_param($stmt, $params[0], ...$params[1]);
    }

    mysqli_stmt_execute($stmt);

    $command = strtolower(strtok(trim($sql), " "));

    if ($command === "select") {
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    if ($command === "insert") {
        $id = mysqli_insert_id($CONN);
        mysqli_stmt_close($stmt);
        return $id;
    }

    if ($command === "update" || $command === "delete") {
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected;
    }

    mysqli_stmt_close($stmt);
    return true;
}
?>