<?php

// the frontend needs a way to fetch the CSRF token from the backend.

require_once __DIR__ . "/middlewares/JsonMiddleware.php";
require_once __DIR__ . "/helpers/Response.php";
require_once __DIR__ . "/helpers/Csrf.php";
require_once __DIR__ . "/controllers/PatientController.php";


setJsonHeaders();

$method = $_SERVER["REQUEST_METHOD"];
$request = $_GET["request"] ?? "";

if ($method === "OPTIONS") {
    sendResponse(200, ["message" => "OK"]);
}


// GET CSFR token

if ($method === "GET" && $request === "csrf") {
    sendResponse(200, [
        "csrf_token" => generateCsrfToken()
    ]);
}

if ($method === "GET" && $request === "patients") {
    PatientController::getAll();
    exit;
}

if ($method === "POST" && $request === "patients") {
    validateCsrfToken();
    PatientController::create();
    exit;
}

if ($method === "PUT" && preg_match('/^patients\/([0-9]+)$/', $request, $matches)) {
    validateCsrfToken();
    PatientController::update($matches[1]);
    exit;
}

if ($method === "DELETE" && preg_match('/^patients\/([0-9]+)$/', $request, $matches)) {
    validateCsrfToken();
    PatientController::delete($matches[1]);
    exit;
}

sendResponse(404, [
    "message" => "Route not found"
]);
