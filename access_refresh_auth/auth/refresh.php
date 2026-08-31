<?php

require_once "../vendor/autoload.php";
require_once "../config/database.php";
require_once "../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

// validate Refresh token

// 1. Check if refresh token cookie exists

if (!isset($_COOKIE["refresh_token"])) {

    http_response_code(401);

    echo json_encode([
        "message" => "Refresh token missing"
    ]);

    exit;
}


$refreshToken = $_COOKIE["refresh_token"];


// 2. Verify the Refresh JWT

try {

    $decoded = JWT::decode(
        $refreshToken,
        new Key($jwtSecret, "HS256")
    );

} catch (Exception $e) {

    http_response_code(401);

    echo json_encode([
        "message" => "Invalid or expired refresh token"
    ]);

    exit;
}


// 3. Make sure this is actually a refresh token

if (!isset($decoded->type) || $decoded->type !== "refresh") {

    http_response_code(401);

    echo json_encode([
        "message" => "Invalid token type"
    ]);

    exit;
}


// 4. Hash the refresh token

$tokenHash = hash("sha256", $refreshToken);


// 5. Find the refresh token in database

$stmt = $conn->prepare(
    "SELECT id, user_id, expires_at, revoked
     FROM refresh_tokens
     WHERE token_hash = ?"
);

$stmt->bind_param("s", $tokenHash);

$stmt->execute();

$result = $stmt->get_result();

$tokenData = $result->fetch_assoc();


// 6. Check if token exists in database

if (!$tokenData) {

    http_response_code(401);

    echo json_encode([
        "message" => "Refresh token not recognized"
    ]);

    exit;
}


// 7. Check if token was revoked

if ($tokenData["revoked"] == 1) {

    http_response_code(401);

    echo json_encode([
        "message" => "Refresh token has been revoked"
    ]);

    exit;
}


// 8. Make sure user ID matches

if ($decoded->user_id != $tokenData["user_id"]) {

    http_response_code(401);

    echo json_encode([
        "message" => "Invalid refresh token"
    ]);

    exit;
}

// after every validation ends.... Refresh token rotation

// Generate new Access Token

$issuedAt = time();

$accessToken = JWT::encode([
    "user_id" => $decoded->user_id,
    "username" => $decoded->username,
    "iat" => $issuedAt,
    "exp" => $issuedAt + $accessTokenExpiry
], $jwtSecret, "HS256");


// Generate new Refresh Token

$newRefreshToken = JWT::encode([
    "user_id" => $decoded->user_id,
    "username" => $decoded->username,
    "type" => "refresh",
    "iat" => $issuedAt,
    "exp" => $issuedAt + $refreshTokenExpiry
], $jwtSecret, "HS256");


// Hash the new Refresh Token

$newRefreshTokenHash = hash(
    "sha256",
    $newRefreshToken
);


// Calculate new Refresh Token expiry

$newRefreshExpiresAt = date(
    "Y-m-d H:i:s",
    $issuedAt + $refreshTokenExpiry
);


// Store new Refresh Token in database

$stmt = $conn->prepare(
    "INSERT INTO refresh_tokens
    (user_id, token_hash, expires_at)
    VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "iss",
    $decoded->user_id,
    $newRefreshTokenHash,
    $newRefreshExpiresAt
);

$stmt->execute();


// Revoke old Refresh Token

$stmt = $conn->prepare(
    "UPDATE refresh_tokens
     SET revoked = 1
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $tokenData["id"]
);

$stmt->execute();


// Set new Access Token cookie

setcookie("access_token", $accessToken, [
    "expires" => $issuedAt + $accessTokenExpiry,
    "httponly" => true,
    "secure" => false,
    "samesite" => "Strict",
    "path" => "/"
]);


// Set new Refresh Token cookie

setcookie("refresh_token", $newRefreshToken, [
    "expires" => $issuedAt + $refreshTokenExpiry,
    "httponly" => true,
    "secure" => false,
    "samesite" => "Strict",
    "path" => "/"
]);


echo json_encode([
    "message" => "Tokens refreshed successfully"
]);