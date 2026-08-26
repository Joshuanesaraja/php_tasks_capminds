<?php

class Database
{
    private $connection;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';

        $this->connection = new mysqli(
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name'],
            $config['db_port']
        );

        if ($this->connection->connect_error) {
            die("Database connection failed: " . $this->connection->connect_error);
        }

        $this->connection->set_charset("utf8mb4");
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
