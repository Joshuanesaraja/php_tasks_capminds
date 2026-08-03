<?php

namespace Utils;

class Validator
{
    public function validateUsername($username)
    {
        if (strlen($username) >= 3) {
            return "Valid";
        } else {
            return "Invalid";
        }
    }

    public function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Valid";
        } else {
            return "Invalid";
        }
    }

    public function validatePassword($password)
    {
        if (strlen($password) >= 8) {
            return "Strong";
        } else {
            return "Weak";
        }
    }
}

// filter_var($email, FILTER_VALIDATE_EMAIL)

// This is a built-in PHP function. It checks if the email follows the correct format.

?>