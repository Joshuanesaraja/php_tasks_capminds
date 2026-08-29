<?php

require_once "../middleware/AuthMiddleware.php";
require_once "../middleware/CsrfMiddleware.php";

header("Content-Type: application/json");

$user = authenticate($jwtSecret);

verifyCsrf();

echo json_encode([
    "message" => "CSRF verification successful",
    "user_id" => $user->user_id
]);