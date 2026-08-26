<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/JWT.php';
require_once __DIR__ . '/../helpers/Encryption.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // REGISTER

    public function register(&$request)
    {
        $body = $request['body'];

        if (
            empty($body['name']) ||
            empty($body['email']) ||
            empty($body['password'])
        ) {
            Response::json([
                'message' => 'Name, email and password are required'
            ], 400);
        }

        $name = trim($body['name']);
        $email = trim($body['email']);
        $password = $body['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json([
                'message' => 'Invalid email address'
            ], 400);
        }

        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser) {
            Response::json([
                'message' => 'Email already registered'
            ], 409);
        }

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $created = $this->userModel->create(
            $name,
            $email,
            $hashedPassword
        );

        if (!$created) {
            Response::json([
                'message' => 'Registration failed'
            ], 500);
        }

        Response::json([
            'message' => 'Registration successful'
        ], 201);
    }

    // LOGIN

    public function login(&$request)
    {
        $body = $request['body'];

        if (
            empty($body['email']) ||
            empty($body['password'])
        ) {
            Response::json([
                'message' => 'Email and password are required'
            ], 400);
        }

        $email = trim($body['email']);
        $password = $body['password'];

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            Response::json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        if (!password_verify($password, $user['password'])) {
            Response::json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $config = require __DIR__ . '/../../config/config.php';

        $issuedAt = time();
        $expiry = $issuedAt + $config['jwt_expiry'];

        $decryptedEmail = decryptData($user['email']);

        $payload = [
            'user_id' => $user['id'],
            'email' => $decryptedEmail,
            'iat' => $issuedAt,
            'exp' => $expiry
        ];

        $token = JWT::generate(
            $payload,
            $config['jwt_secret']
        );

        Response::json([
            'message' => 'Login successful',
            'token' => $token,
            'expires_at' => $expiry
        ]);
    }
}