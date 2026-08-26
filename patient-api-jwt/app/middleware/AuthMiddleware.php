<?php

require_once __DIR__ . '/../helpers/JWT.php';
require_once __DIR__ . '/../helpers/Response.php';

class AuthMiddleware
{
    public static function handle(&$request)
    {
        $config = require __DIR__ . '/../../config/config.php';


        // authorization header eg. Authorization: Bearer abc123
        $authorization = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        if (empty($authorization)) {
            Response::json([
                'message' => 'Authorization token required'
            ], 401);
        }

        // extract Bearer token
        if (
            !preg_match(
                '/^Bearer\s+(.+)$/i',
                $authorization,
                $matches
            )
        ) {
            Response::json([
                'message' => 'Invalid Authorization header'
            ], 401);
        }

        $token = $matches[1];

        // verify the extracted token
        $userData = JWT::verify(
            $token,
            $config['jwt_secret']
            // The server uses the same secret that was used when the JWT was generated.
        );

        if (!$userData) {
            Response::json([
                'message' => 'Invalid or expired token'
            ], 401);
        }

        // if verification success we will recieve userdata 
        $request['user'] = $userData;
    }
}
