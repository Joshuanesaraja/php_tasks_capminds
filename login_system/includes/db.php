<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "login";
$port = 3308;

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database,
    $port
);

//mysqli is a class that represents a connection between PHP and a MySQL database.

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
    // die is equivalent to exit()
}

?>