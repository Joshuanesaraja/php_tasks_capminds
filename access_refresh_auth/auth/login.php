<?php

require_once "../vendor/autoload.php";
require_once "../config/database.php";
require_once "../config/jwt.php";

use Firebase\JWT\JWT;

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

// generate our Access Token.

$issuedAt = time();
$accessToken = JWT::encode([
    "user_id" => $user["id"],
    "username" => $user["username"],
    "iat" => $issuedAt,
    "exp" => $issuedAt + $accessTokenExpiry
], $jwtSecret, "HS256");

// generate refresh token

$refreshToken = JWT::encode([
    "user_id" => $user["id"],
    "username" => $user["username"],
    "type" => "refresh",
    "iat" => $issuedAt,
    "exp" => $issuedAt + $refreshTokenExpiry
], $jwtSecret, "HS256");

// hash refresh token to store it in db

$refreshTokenHash = hash("sha256", $refreshToken);

$refreshExpiresAt = date(
    "Y-m-d H:i:s",
    time() + $refreshTokenExpiry
);

$stmt = $conn->prepare(
    "INSERT INTO refresh_tokens
    (user_id, token_hash, expires_at)
    VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "iss",
    $user["id"],
    $refreshTokenHash,
    $refreshExpiresAt
);

$stmt->execute();



// storing access token in http only cookie

// set cookie tells the browser: Store this access token as a cookie.
// The browser will then automatically send it with requests to our server.

setcookie("access_token", $accessToken, [
    "expires" => time() + $accessTokenExpiry,
    "httponly" => true,
    "secure" => false,
    // We're currently developing with:http://localhost So we can't require HTTPS yet.

    "samesite" => "Strict",
    // tells the browser to restrict sending the cookie in cross-site situations.

    "path" => "/"
    // Send this cookie for all paths on this host.
]);

// storing refresh token in http only cookie

setcookie("refresh_token", $refreshToken, [
    "expires" => time() + $refreshTokenExpiry,
    "httponly" => true,
    "secure" => false,
    "samesite" => "Strict",
    "path" => "/"
]);


echo json_encode([
    "message" => "Login successful"
]);
