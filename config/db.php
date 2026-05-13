<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change if your MySQL user is different
define('DB_PASS', 'pass123');           // Add your MySQL password if you have one
define('DB_NAME', 'sleep_tracker');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
?>