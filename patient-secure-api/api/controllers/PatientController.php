<?php

require_once __DIR__ . "/../models/Patient.php";

class PatientController
{
    // POST

    public static function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data["name"]) ||
            !isset($data["email"]) ||
            !isset($data["phone"]) ||
            !isset($data["diagnosis"])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            return;
        }

        $id = Patient::create($data);

        http_response_code(201);
        echo json_encode([
            "message" => "Patient created successfully",
            "id" => $id
        ]);
    }

    // GET

    public static function getAll()
    {
        $patients = Patient::findAll();

        echo json_encode([
            "patients" => $patients
        ]);
    }

    // UPDATE

    public static function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data["name"]) ||
            !isset($data["email"]) ||
            !isset($data["phone"]) ||
            !isset($data["diagnosis"])
        ) {
            sendResponse(400, [
                "message" => "All fields are required"
            ]);
        }

        $updated = Patient::update($id, $data);

        if ($updated === 0) {
            sendResponse(404, [
                "message" => "Patient not found"
            ]);
        }

        sendResponse(200, [
            "message" => "Patient updated successfully"
        ]);
    }

    // DELETE

    public static function delete($id)
    {
        $deleted = Patient::delete($id);

        if ($deleted === 0) {
            sendResponse(404, [
                "message" => "Patient not found"
            ]);
        }

        sendResponse(200, [
            "message" => "Patient deleted successfully"
        ]);
    }
}
