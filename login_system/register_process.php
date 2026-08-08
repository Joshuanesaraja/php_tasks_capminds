<?php

session_start();

require 'includes/validation.php';
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $theme = $_POST['theme'] ?? 'light';


    // Validate username


    $usernameError = validateUsername($username);

    if ($usernameError != "") {

        $_SESSION['register_error'] = $usernameError;

        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'theme' => $theme
        ];

        header("Location: register.php");
        exit();
    }


    // Validate email

    $emailError = validateEmail($email);

    if ($emailError != "") {

        $_SESSION['register_error'] = $emailError;

        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'theme' => $theme
        ];

        header("Location: register.php");
        exit();
    }


    // Validate password


    $passwordError = validatePassword($password);

    if ($passwordError != "") {

        $_SESSION['register_error'] = $passwordError;

        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'theme' => $theme
        ];

        header("Location: register.php");
        exit();
    }

    // Confirm password


    if ($password !== $confirmPassword) {

        $_SESSION['register_error'] = "Passwords do not match.";

        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'theme' => $theme
        ];

        header("Location: register.php");
        exit();
    }

    // Check if username/email exists
 

    $sql = "SELECT user_id
            FROM users
            WHERE username = ? OR email = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ss", $username, $email);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $_SESSION['register_error'] =
            "Username or email already exists.";

        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'theme' => $theme
        ];

        header("Location: register.php");
        exit();
    }

    $stmt->close();


    // Hash password


    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Insert new user


    $sql = "INSERT INTO users
            (username, email, password, theme)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $username,
        $email,
        $hashedPassword,
        $theme
    );


    if ($stmt->execute()) {

        $_SESSION['register_success'] =
            "Registration successful!";

        header("Location: login.php");
        exit();

    } else {

        $_SESSION['register_error'] =
            "Registration failed.";

        $_SESSION['register_old'] = [
            'username' => $username,
            'email' => $email,
            'theme' => $theme
        ];

        header("Location: register.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}

?>