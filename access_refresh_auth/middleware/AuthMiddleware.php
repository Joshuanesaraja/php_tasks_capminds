<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/jwt.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authenticate($jwtSecret)
{
    if (
        !isset($_COOKIE["access_token"]) ||
        $_COOKIE["access_token"] === "" ||
        $_COOKIE["access_token"] === "deleted"
    ) {
        http_response_code(401);

        echo json_encode([
            "message" => "Access token missing"
        ]);

        exit;
    }

    $token = $_COOKIE["access_token"];

    try {

        $decoded = JWT::decode(
            $token,
            new Key($jwtSecret, "HS256")
        );

        return $decoded;
    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode([
            "message" => "Invalid or expired access token"
        ]);

        exit;
    }
}
