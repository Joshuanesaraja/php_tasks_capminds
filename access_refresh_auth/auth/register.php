<?php

require_once "../config/database.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$password = $data["password"] ?? "";

if (empty($username) || empty($password)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Username and password are required"
    ]);

    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);

    echo json_encode([
        "message" => "Password must be at least 6 characters"
    ]);

    exit;
}

$stmt = $conn->prepare(
    "SELECT id FROM users WHERE username = ?"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);

    echo json_encode([
        "message" => "Username already exists"
    ]);

    exit;
}

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare(
    "INSERT INTO users (username, password)
     VALUES (?, ?)"
);

$stmt->bind_param(
    "ss",
    $username,
    $hashedPassword
);

if ($stmt->execute()) {

    http_response_code(201);

    echo json_encode([
        "message" => "User registered successfully"
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        "message" => "Registration failed"
    ]);
}