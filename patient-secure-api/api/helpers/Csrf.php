<?php

function generateCsrfToken()
{
    session_start();

    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        // new tokenis generated using bin2hex and stored in session
    }

    return $_SESSION["csrf_token"];
}

// for a protected request, the frontend will send
// X-CSRF-Token: <token>

function validateCsrfToken()
{
    session_start();

    $headers = getallheaders();
    $csrfToken = $headers["X-CSRF-Token"] ?? "";

    if (
        empty($_SESSION["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        http_response_code(403);
        // 403 Forbidden
        
        echo json_encode(["message" => "Invalid CSRF token"]);
        exit;
    }
}