<?php

session_start();


// ============================================================
// CHECK LOGIN
// ============================================================

if (!isset($_SESSION["user_id"])) {

    header("Location: ../account/login.php");

    exit;

}




// ============================================================
// ADMIN ACCESS CHECK
// ============================================================

function requireAdmin()
{
    if (
        !isset($_SESSION["role"]) ||
        $_SESSION["role"] !== "admin"
    ) {

        http_response_code(403);

        die("
            <div style='
                font-family: Arial;
                text-align: center;
                margin-top: 100px;
            '>

                <h2>Access Denied</h2>

                <p>
                    You do not have permission to access this page.
                </p>

                <a href='../index.php'>
                    Back to Dashboard
                </a>

            </div>
        ");

    }
}

?>

