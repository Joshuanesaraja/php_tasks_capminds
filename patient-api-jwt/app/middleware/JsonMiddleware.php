<?php

class JsonMiddleware
{
    public static function handle(&$request)
    // &$request this allows the same $request data to be passed through the application and modified. called as pass by reference
    {
        header('Content-Type: application/json');

        $method = strtoupper($request['method']);

        // No JSON body required for GET, DELETE routes
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {

            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (stripos($contentType, 'application/json') !== 0) {
                Response::json([
                    'message' => 'Content-Type must be application/json'
                ], 400);
            }

            $rawBody = file_get_contents('php://input');
            // this contains the body as json object

            if (trim($rawBody) === '') {
                Response::json([
                    'message' => 'Request body cannot be empty'
                ], 400);
            }

            $decodedBody = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Response::json([
                    'message' => 'Invalid JSON payload'
                ], 400);
            }

            $request['body'] = $decodedBody;
        }
    }
}