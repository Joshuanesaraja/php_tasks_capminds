<?php
// Main API router

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/Patient.php";
require_once __DIR__ . "/controllers/PatientController.php";
require_once __DIR__ . "/middlewares/JsonMiddleware.php";
require_once __DIR__ . "/helpers/Response.php";

JsonMiddleware::handle();

$patientModel = new Patient($conn);

// Our Controller requires a Model
$patientController = new PatientController($patientModel);

$request = $_GET['request'] ?? '';

$method = $_SERVER['REQUEST_METHOD'];


// creating endpoints for routing

// getting entire data => GET /api/patients

if ($method === "GET" && $request === "patients") {

    $patients = $patientController->getAllPatients();

    Response::json(
        200,
        [
            "status" => true,
            "message" => "Patients retrieved successfully",
            "data" => $patients
        ]
    );
}
// to check use GET http://localhost/php_tasks_combined/patient-api/api/patients



// getting data for a particular id like => GET /api/patients/5

if ($method === "GET" && preg_match('/^patients\/([0-9]+)$/', $request, $matches)) {

    // request=patients/5 this comes from the response.php

    // $matches[0] = "patients/5"; -> entire matched string
    // $matches[1] = "5"; -> the content captured by ([0-9]+)

    $id = $matches[1];

    $patient = $patientController->getPatientById($id);

    if ($patient === null) {

        Response::json(
            404,
            [
                "status" => false,
                "message" => "Patient not found",
                "data" => []
            ]
        );
    }

    Response::json(
        200,
        [
            "status" => true,
            "message" => "Patient retrieved successfully",
            "data" => $patient
        ]
    );
}
// to check use GET http://localhost/php_tasks_combined/patient-api/api/patients/1


// Post method

if ($method === "POST" && $request === "patients") {

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $result = $patientController->createPatient($data);

    if ($result) {

        Response::json(
            201,
            [
                "status" => true,
                "message" => "Patient created successfully",
                "data" => []
            ]
        );
    }

    Response::json(
        500,
        [
            "status" => false,
            "message" => "Failed to create patient",
            "data" => []
        ]
    );
}


// PUT update patient
if ($method === "PUT" && preg_match('/^patients\/([0-9]+)$/', $request, $matches))
    {

    $id = $matches[1];

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    $result = $patientController->updatePatient($id, $data);

    if ($result) {

        Response::json(
            200,
            [
                "status" => true,
                "message" => "Patient updated successfully",
                "data" => []
            ]
        );
    }

    Response::json(
        500,
        [
            "status" => false,
            "message" => "Failed to update patient",
            "data" => []
        ]
    );
}

// to check use PUT http://localhost/php_tasks_combined/patient-api/api/patients/1


// DELETE patient
if ($method === "DELETE" && preg_match('/^patients\/([0-9]+)$/', $request, $matches))
    {

    $id = $matches[1];

    $result = $patientController->deletePatient($id);

    if ($result) {

        Response::json(
            200,
            [
                "status" => true,
                "message" => "Patient deleted successfully",
                "data" => []
            ]
        );
    }

    Response::json(
        500,
        [
            "status" => false,
            "message" => "Failed to delete patient",
            "data" => []
        ]
    );
}
// to check use DELETE http://localhost/php_tasks_combined/patient-api/api/patients/1

// Route Not Found

Response::json(
    404,
    [
        "status" => false,
        "message" => "Endpoint not found",
        "data" => []
    ]
);
