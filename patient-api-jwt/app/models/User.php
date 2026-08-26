<?php

require_once __DIR__ . '/../core/Database.php';

class User
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    // check whether user is already existing

    public function findByEmail($email)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }


    // POST create new user

    public function create($name, $email, $hashedPassword)
    {
        $stmt = $this->connection->prepare(
            "INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sss",
            $name,
            $email,
            $hashedPassword
        );

        return $stmt->execute();
    }
}