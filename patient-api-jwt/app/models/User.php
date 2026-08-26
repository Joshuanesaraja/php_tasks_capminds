<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../helpers/Encryption.php';

class User
{
    private $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    // Find user by decrypted email

    public function findByEmail($email)
    {
        $result = $this->connection->query(
            "SELECT * FROM users ORDER BY id ASC"
        );

        $users = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($users as $user) {

            $decryptedEmail = decryptData($user['email']);

            if ($decryptedEmail === $email) {
                return $user;
            }
        }

        return null;
    }

    // Create user

    public function create($name, $email, $hashedPassword)
    {
        $encryptedName = encryptData($name);
        $encryptedEmail = encryptData($email);

        $stmt = $this->connection->prepare(
            "INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sss",
            $encryptedName,
            $encryptedEmail,
            $hashedPassword
        );

        return $stmt->execute();
    }
}