<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../helpers/Encryption.php';

class Patient
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    // GET ALL PATIENTS FOR LOGGED-IN USER

    public function getAll($userId)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM patients
             WHERE user_id = ?
             ORDER BY id DESC"
        );

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        $patients = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($patients as &$patient) {

            $patient['name'] = decryptData($patient['name']);
            $patient['age'] = (int) decryptData($patient['age']);
            $patient['gender'] = decryptData($patient['gender']);
            $patient['phone'] = decryptData($patient['phone']);
            $patient['address'] = decryptData($patient['address']);
        }

        return $patients;
    }

    // CREATE

    public function create(
        $userId,
        $name,
        $age,
        $gender,
        $phone,
        $address
    ) {
        $encryptedName = encryptData($name);
        $encryptedAge = encryptData((string)$age);
        $encryptedGender = encryptData($gender);
        $encryptedPhone = encryptData($phone);
        $encryptedAddress = encryptData($address);

        $stmt = $this->connection->prepare(
            "INSERT INTO patients
            (user_id, name, age, gender, phone, address)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "isssss",
            $userId,
            $encryptedName,
            $encryptedAge,
            $encryptedGender,
            $encryptedPhone,
            $encryptedAddress
        );

        return $stmt->execute();
    }

    // FIND BY ID FOR LOGGED-IN USER

    public function findById($id, $userId)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM patients
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        $patient = $result->fetch_assoc();

        if (!$patient) {
            return null;
        }

        $patient['name'] = decryptData($patient['name']);
        $patient['age'] = (int) decryptData($patient['age']);
        $patient['gender'] = decryptData($patient['gender']);
        $patient['phone'] = decryptData($patient['phone']);
        $patient['address'] = decryptData($patient['address']);

        return $patient;
    }

    // PUT/ UPDATE

    public function update(
        $id,
        $userId,
        $name,
        $age,
        $gender,
        $phone,
        $address
    ) {
        $encryptedName = encryptData($name);
        $encryptedAge = encryptData((string)$age);
        $encryptedGender = encryptData($gender);
        $encryptedPhone = encryptData($phone);
        $encryptedAddress = encryptData($address);

        $stmt = $this->connection->prepare(
            "UPDATE patients
             SET name = ?,
                 age = ?,
                 gender = ?,
                 phone = ?,
                 address = ?
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param(
            "sssssii",
            $encryptedName,
            $encryptedAge,
            $encryptedGender,
            $encryptedPhone,
            $encryptedAddress,
            $id,
            $userId
        );

        return $stmt->execute();
    }

    // DELETE

    public function delete($id, $userId)
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM patients
             WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $id, $userId);

        return $stmt->execute();
    }
}