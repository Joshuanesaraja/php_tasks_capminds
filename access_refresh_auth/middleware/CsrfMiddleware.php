<?php

session_start();

function verifyCsrf()
{
    $csrfToken = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";

    if (
        empty($csrfToken) ||
        empty($_SESSION["csrf_token"]) ||
        !hash_equals(
            $_SESSION["csrf_token"],
            $csrfToken
        )
    ) {
        http_response_code(403);

        echo json_encode([
            "message" => "Invalid CSRF token"
        ]);

        exit;
    }
}