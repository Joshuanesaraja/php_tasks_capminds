<?php
session_start();

require 'includes/validation.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    // Validation
    $usernameError = validateUsername($username);

    if ($usernameError != "") {
        $_SESSION['error'] = $usernameError;
        header("Location: login.php");
        exit();
    }

    $emailError = validateEmail($email);

    if ($emailError != "") {
        $_SESSION['error'] = $emailError;
        header("Location: login.php");
        exit();
    }

    $passwordError = validatePassword($password);

    if ($passwordError != "") {
        $_SESSION['error'] = $passwordError;
        header("Location: login.php");
        exit();
    }
    // Dummy Users
    $users = [
        [
            "user_id" => 1,
            "username" => "user1",
            "email" => "user1@example.com",
            "password" => "User1@123",
            "theme" => "dark"
        ],
        [
            "user_id" => 2,
            "username" => "user2",
            "email" => "user2@example.com",
            "password" => "User2@123",
            "theme" => "warm"
        ],
        [
            "user_id" => 3,
            "username" => "user3",
            "email" => "user3@example.com",
            "password" => "User3@123",
            "theme" => "light"
        ]
    ];

    $authenticated = false;

    foreach ($users as $user) {

        if (
            $username == $user['username'] &&
            $email == $user['email'] &&
            $password == $user['password']
        ) {

            $authenticated = true;

            // Store Session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['theme'] = $user['theme'];

            // Remember Me
            if ($remember) {
                setcookie("remember_username", $user['username'], time() + 60, "/");
                setcookie("user_theme", $user['theme'], time() + 60, "/");
            } else {
                setcookie("remember_username", "", time() - 60, "/");
                setcookie("user_theme", $user['theme'], time() + 60, "/");
            }

            header("Location: dashboard.php");
            exit();
        }
    }

    if (!$authenticated) {
        $_SESSION['error'] = "Invalid Login Credentials.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
