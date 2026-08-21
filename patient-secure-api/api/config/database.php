<?php

// Connect PHP to MySQL

$host = "localhost";
$username = "root";
$password = "";
$database = "patient_secure_api";
$port = 3308;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
