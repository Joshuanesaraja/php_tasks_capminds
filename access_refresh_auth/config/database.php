<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "access_refresh_auth";
$port = 3308;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}