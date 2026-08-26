<?php

require_once __DIR__ . '/../core/Database.php';

class Patient
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    // GET

    public function getAll()
    {
        $result = $this->connection->query(
            "SELECT * FROM patients ORDER BY id DESC"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // POST

    public function create($name, $age, $gender, $phone, $address)
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO patients
        (name, age, gender, phone, address)
        VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "sisss",
            $name,
            $age,
            $gender,
            $phone,
            $address
        );

        return $stmt->execute();
    }

    // find by id

    public function findById($id)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM patients WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    // UPDATE

    public function update($id, $name, $age, $gender, $phone, $address)
    {
        $stmt = $this->connection->prepare(
            "UPDATE patients
         SET name = ?, age = ?, gender = ?, phone = ?, address = ?
         WHERE id = ?"
        );

        $stmt->bind_param(
            "sisssi",
            $name,
            $age,
            $gender,
            $phone,
            $address,
            $id
        );

        return $stmt->execute();
    }

    // DELETE 

    public function delete($id)
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM patients WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
