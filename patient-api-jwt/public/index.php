<?php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/middleware/JsonMiddleware.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/controllers/PatientController.php';
require_once __DIR__ . '/../app/models/Patient.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
// this gets patient-api-jwt/api/register

// we extract everything beginning with api/

$apiPosition = strpos($path, 'api/');

if ($apiPosition !== false) {
    $path = substr($path, $apiPosition);
}

// request information is created

$request = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $path,
    'body' => []
];

JsonMiddleware::handle($request);

$router = new Router();

$router->add(
    'POST',
    '#^api/register$#',
    [AuthController::class, 'register']
);

$router->add(
    'POST',
    '#^api/login$#',
    [AuthController::class, 'login']
);

$router->add(
    'GET',
    '#^api/patients$#',
    [PatientController::class, 'getAll'],
    [AuthMiddleware::class]
);

$router->add(
    'POST',
    '#^api/patients$#',
    [PatientController::class, 'create'],
    [AuthMiddleware::class]
);

$router->add(
    'PUT',
    '#^api/patients/([0-9]+)$#',
    [PatientController::class, 'update'],
    [AuthMiddleware::class]
);

$router->add(
    'DELETE',
    '#^api/patients/([0-9]+)$#',
    [PatientController::class, 'delete'],
    [AuthMiddleware::class]
);

$router->dispatch(
    $request['method'],
    $request['uri'],
    $request
);
