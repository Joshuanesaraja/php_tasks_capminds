<?php

// Database/model operations

class Patient
{
    private $conn;
    // We want that connection conn to be used internally by the Patient class, not accessed directly from outside.

    // if we use any other access modifiers then you're allowing outside code to directly manipulate an important internal property.


    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    // When we create the Model, we'll pass the database connection into it.

    public function getAllPatients()
    {
        $sql = "SELECT * FROM patients";

        return $this->conn->query($sql);
    }

    public function getPatientById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM patients WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result();
    }

    public function createPatient($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO patients (name, age, gender, phone)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "siss",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone']
        );

        return $stmt->execute();
    }

    public function updatePatient($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE patients
             SET name = ?, age = ?, gender = ?, phone = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sissi",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $id
        );

        return $stmt->execute();
    }

    public function deletePatient($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM patients WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}