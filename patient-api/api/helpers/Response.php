<?php

// Format API responses

class Response
{
    // We'll be able to call it without creating a Response object:
    public static function json($statusCode, $data)
    {
        http_response_code($statusCode);

        header("Content-Type: application/json");

        echo json_encode($data);

        exit;
    }
}

