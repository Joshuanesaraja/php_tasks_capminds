<?php

session_start();

require 'includes/validation.php';
require 'includes/db.php';

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

    // Find user in database

    $sql = "SELECT *
            FROM users
            WHERE username = ? AND email = ?";
    // The ? values are placeholders. it gets username AND email match what the person entered.

    $stmt = $conn->prepare($sql);
    // This says prepare this SQL query so I'm going to provide the values separately."
    // $conn is our MySQL connection from db.php.

    $stmt->bind_param("ss", $username, $email);
    // bind_param -> Binds variables to a prepared statement as parameters
    // Put the username and email into the "?"
    // ss means type of two values string, string

    $stmt->execute();

    $result = $stmt->get_result();

    // Check if user exists

    if ($result->num_rows === 0) {

        $_SESSION['error'] = "Invalid Login Credentials.";
        header("Location: login.php");
        exit();
    }

    $user = $result->fetch_assoc();
    // fetch_assoc() gets the database row as an associative array. 
    // associative array -> just like we had the dummy users array


    // Verify hashed password


    if (password_verify($password, $user['password'])) {

        // Store session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['theme'] = $user['theme'];

        // Remember Me
        if ($remember) {

            setcookie(
                "remember_username",
                $user['username'],
                time() + 60,
                "/"
            );

            setcookie(
                "user_theme",
                $user['theme'],
                time() + 60,
                "/"
            );

        } else {

            setcookie(
                "remember_username",
                "",
                time() - 60,
                "/"
            );

            setcookie(
                "user_theme",
                $user['theme'],
                time() + 60,
                "/"
            );
        }

        header("Location: dashboard.php");
        exit();

    } else {

        $_SESSION['error'] = "Invalid Login Credentials.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}

?>