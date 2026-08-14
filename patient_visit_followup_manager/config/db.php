<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "patient_visit_manager";
$port = 3308;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>

<!-- utf8mb4 supports 4-byte Unicode characters, including many emojis and other less-common Unicode characters. -->
<!-- utf8mb4 helps ensure those characters are stored and retrieved correctly. -->