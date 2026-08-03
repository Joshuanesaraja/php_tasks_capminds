<?php

namespace Utils;

class User
{
    public $username;
    public $email;
    public $password;

    public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function displayUser()
    {
        echo "Username: " . $this->username . "<br>";
        echo "Email: " . $this->email . "<br>";
        echo "Password: " . $this->password . "<br> <br>";
    }
}

?>