<?php

require_once "../config/database.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"] ?? "";
$password = $data["password"] ?? "";

if (empty($username) || empty($password)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Username and password are required"
    ]);

    exit;
}

$stmt = $conn->prepare(
    "SELECT id, username, password
     FROM users
     WHERE username = ?"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if (!$user || !password_verify($password, $user["password"])) {
    http_response_code(401);

    echo json_encode([
        "message" => "Invalid username or password"
    ]);

    exit;
}

echo json_encode([
    "message" => "Login successful",
    "user_id" => $user["id"],
    "username" => $user["username"]
]);