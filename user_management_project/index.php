<?php

// require_once is used for important files.
// If these files are missing, the application cannot run.
require_once "utils/User.php";
require_once "utils/Validator.php";
$users = require_once "data/users.php";

// include_once is used for helper functions.
// If this file is missing, PHP will show a warning but continue.
include_once "utils/helpers.php";

use Utils\User;
use Utils\Validator as UserValidator;

$validator = new UserValidator();

printHeading("User Management System");

foreach ($users as $userData) {

    $user = new User(
        $userData["username"],
        $userData["email"],
        $userData["password"]
    );

    $user->displayUser();

    echo "Username Validation: " .
        $validator->validateUsername($user->username) . "<br>";

    echo "Email Validation: " .
        $validator->validateEmail($user->email) . "<br>";

    echo "Password Validation: " .
        $validator->validatePassword($user->password) . "<br>";

    printLine();
}

?>

<!-- http://localhost/php_tasks_combined/user_management_project/index.php -->