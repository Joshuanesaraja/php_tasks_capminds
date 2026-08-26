<?php

require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../helpers/Response.php';

class PatientController
{
    private $patientModel;

    public function __construct()
    {
        $this->patientModel = new Patient();
    }

    // GET

    public function getAll(&$request)
    {
        $userId = $request['user']['user_id'];

        $patients = $this->patientModel->getAll($userId);

        Response::json([
            'user' => $request['user']['email'],
            'patients' => $patients
        ]);
    }

    // POST

    public function create(&$request)
    {
        $body = $request['body'];

        if (
            empty($body['name']) ||
            empty($body['age']) ||
            empty($body['gender']) ||
            empty($body['phone']) ||
            empty($body['address'])
        ) {
            Response::json([
                'message' => 'All fields are required'
            ], 400);
        }

        $userId = $request['user']['user_id'];

        $created = $this->patientModel->create(
            $userId,
            trim($body['name']),
            (int)$body['age'],
            trim($body['gender']),
            trim($body['phone']),
            trim($body['address'])
        );

        if (!$created) {
            Response::json([
                'message' => 'Patient creation failed'
            ], 500);
        }

        Response::json([
            'message' => 'Patient created successfully'
        ], 201);
    }

    // PUT

    public function update(&$request, $matches)
    {
        $id = (int)$matches[1];

        $userId = $request['user']['user_id'];

        $patient = $this->patientModel->findById(
            $id,
            $userId
        );

        if (!$patient) {
            Response::json([
                'message' => 'Patient not found or access denied'
            ], 404);
        }

        $body = $request['body'];

        if (
            empty($body['name']) ||
            empty($body['age']) ||
            empty($body['gender']) ||
            empty($body['phone']) ||
            empty($body['address'])
        ) {
            Response::json([
                'message' => 'All fields are required'
            ], 400);
        }

        $updated = $this->patientModel->update(
            $id,
            $userId,
            trim($body['name']),
            (int)$body['age'],
            trim($body['gender']),
            trim($body['phone']),
            trim($body['address'])
        );

        if (!$updated) {
            Response::json([
                'message' => 'Patient update failed'
            ], 500);
        }

        Response::json([
            'message' => 'Patient updated successfully'
        ]);
    }

    // DELETE

    public function delete(&$request, $matches)
    {
        $id = (int)$matches[1];

        $userId = $request['user']['user_id'];

        $patient = $this->patientModel->findById(
            $id,
            $userId
        );

        if (!$patient) {
            Response::json([
                'message' => 'Patient not found or access denied'
            ], 404);
        }

        $deleted = $this->patientModel->delete(
            $id,
            $userId
        );

        if (!$deleted) {
            Response::json([
                'message' => 'Patient deletion failed'
            ], 500);
        }

        Response::json([
            'message' => 'Patient deleted successfully'
        ]);
    }
}