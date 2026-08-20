<?php

// Handle API requests and call the model

class PatientController
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getAllPatients()
    {
        $result = $this->model->getAllPatients();

        $patients = [];

        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }

        return $patients;
    }

    public function getPatientById($id)
    {
        $result = $this->model->getPatientById($id);

        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function createPatient($data)
    {
        return $this->model->createPatient($data);
    }

    public function updatePatient($id, $data)
    {
        return $this->model->updatePatient($id, $data);
    }

    public function deletePatient($id)
    {
        return $this->model->deletePatient($id);
    }
}