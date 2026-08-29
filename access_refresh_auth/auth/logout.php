<?php

require_once "../config/database.php";

header("Content-Type: application/json");


// 1. Check if refresh token exists

if (isset($_COOKIE["refresh_token"])) {

    $refreshToken = $_COOKIE["refresh_token"];

    // Hash the refresh token
    $tokenHash = hash("sha256", $refreshToken);

    // Revoke the refresh token
    $stmt = $conn->prepare(
        "UPDATE refresh_tokens
         SET revoked = 1
         WHERE token_hash = ?"
    );

    $stmt->bind_param("s", $tokenHash);

    $stmt->execute();
}


// 2. Delete access token cookie

setcookie("access_token", "", [
    "expires" => time() - 3600,
    "httponly" => true,
    "secure" => false,
    "samesite" => "Strict",
    "path" => "/"
]);


// 3. Delete refresh token cookie

setcookie("refresh_token", "", [
    "expires" => time() - 3600,
    "httponly" => true,
    "secure" => false,
    "samesite" => "Strict",
    "path" => "/"
]);


// 4. Response

echo json_encode([
    "message" => "Logout successful"
]);