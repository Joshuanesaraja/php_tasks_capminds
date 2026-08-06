<?php


// no needed to check empty fields because we mentioned every field as "required" in our input form in login.php 


function validateUsername($username)
{
    // if (empty($username)) {
    //     return "Username is required";
    // } no needed because we mentioned this field is "required" in our input form in login.php 

    if (strlen($username) < 2) {
        return "Username must be at least 2 characters";
    }

    if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        return "Username can contain only letters, numbers, and underscore";
    }

    return "";
}

function validateEmail($email)
{
    // if (empty($email)) {
    //     return "Email is required";
    // }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }

    return "";
}

function validatePassword($password)
{
    // if (empty($password)) {
    //     return "Password is required";
    // }

    if (strlen($password) < 8) {
        return "Password must be at least 8 characters";
    }

    if (!preg_match("/[A-Z]/", $password)) {
        return "Password must contain at least one uppercase letter";
    }

    if (!preg_match("/[a-z]/", $password)) {
        return "Password must contain at least one lowercase letter";
    }

    if (!preg_match("/[0-9]/", $password)) {
        return "Password must contain at least one number";
    }

    if (!preg_match("/[\W]/", $password)) {
        return "Password must contain at least one special character";
    }

    return "";
}
