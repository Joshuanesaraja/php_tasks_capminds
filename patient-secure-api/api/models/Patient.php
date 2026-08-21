<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/Encryption.php";

class Patient
{

    // POST
    
    public static function create($data)
    {
        global $conn;

        $name = $data["name"];
        $email = encryptData($data["email"]);
        $phone = encryptData($data["phone"]);
        $diagnosis = encryptData($data["diagnosis"]);

        $stmt = $conn->prepare(
            "INSERT INTO patients (name, email, phone, diagnosis)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $phone,
            $diagnosis
        );

        $stmt->execute();

        return $conn->insert_id;
    }

    // GET

    public static function findAll()
    {
        global $conn;

        $result = $conn->query(
            "SELECT id, name, email, phone, diagnosis, created_at
             FROM patients"
        );

        $patients = [];

        while ($row = $result->fetch_assoc()) {
            $row["email"] = decryptData($row["email"]);
            $row["phone"] = decryptData($row["phone"]);
            $row["diagnosis"] = decryptData($row["diagnosis"]);

            $patients[] = $row;
        }

        return $patients;
    }

    // UPDATE

    public static function update($id, $data)
    {
        global $conn;

        $name = $data["name"];
        $email = encryptData($data["email"]);
        $phone = encryptData($data["phone"]);
        $diagnosis = encryptData($data["diagnosis"]);

        $stmt = $conn->prepare(
            "UPDATE patients
         SET name = ?, email = ?, phone = ?, diagnosis = ?
         WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssi",
            $name,
            $email,
            $phone,
            $diagnosis,
            $id
        );

        $stmt->execute();

        return $stmt->affected_rows;
    }

    // DELETE

    public static function delete($id)
    {
        global $conn;

        $stmt = $conn->prepare(
            "DELETE FROM patients WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->affected_rows;
    }
}
