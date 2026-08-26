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
        $patients = $this->patientModel->getAll();

        Response::json([
            'user' => $request['user']['email'],
            'patients' => $patients
        ]);
    }

    // POST/ CREATE

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

        $created = $this->patientModel->create(
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

    // PUT/ UPDATE 

    public function update(&$request, $matches)
    {
        $id = (int)$matches[1];

        $patient = $this->patientModel->findById($id);

        if (!$patient) {
            Response::json([
                'message' => 'Patient not found'
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

        $patient = $this->patientModel->findById($id);

        if (!$patient) {
            Response::json([
                'message' => 'Patient not found'
            ], 404);
        }

        $deleted = $this->patientModel->delete($id);

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
