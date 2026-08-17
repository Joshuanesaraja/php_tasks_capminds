<?php

session_start();

require_once "../config/db.php";


// ============================================================
// CHECK REQUEST METHOD
// ============================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit;
}


// ============================================================
// GET FORM INPUT
// ============================================================

$username = trim($_POST["username"] ?? "");

$password = $_POST["password"] ?? "";


// ============================================================
// VALIDATION
// ============================================================

if ($username === "" || $password === "") {

    $_SESSION["login_error"] =
        "Username and password are required.";

    header("Location: login.php");
    exit;
}


// ============================================================
// FIND USER
// ============================================================

$sql = "
    SELECT
        user_id,
        username,
        password,
        role

    FROM users

    WHERE username = ?

    LIMIT 1
";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    die("Prepare failed: " . $conn->error);
}


$stmt->bind_param(
    "s",
    $username
);


$stmt->execute();


$result = $stmt->get_result();


// ============================================================
// CHECK USER
// ============================================================

if ($result->num_rows !== 1) {

    $_SESSION["login_error"] =
        "Invalid username or password.";

    $stmt->close();
    $conn->close();

    header("Location: login.php");
    exit;
}


$user = $result->fetch_assoc();


// ============================================================
// VERIFY PASSWORD
// ============================================================

if (!password_verify(
    $password,
    $user["password"]
)) {

    $_SESSION["login_error"] =
        "Invalid username or password.";

    $stmt->close();
    $conn->close();

    header("Location: login.php");
    exit;
}


// ============================================================
// LOGIN SUCCESS
// ============================================================

session_regenerate_id(true);


$_SESSION["user_id"] = $user["user_id"];

$_SESSION["username"] = $user["username"];

$_SESSION["role"] = $user["role"];


// ============================================================
// CLEAN UP
// ============================================================

$stmt->close();

$conn->close();


// ============================================================
// REDIRECT TO DASHBOARD
// ============================================================

header("Location: ../index.php");

exit;
