<?php

session_start();


// If already logged in, go to dashboard

if (isset($_SESSION["user_id"])) {

    header("Location: ../index.php");
    exit;
}


$error = $_SESSION["login_error"] ?? "";

unset($_SESSION["login_error"]);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login - Patient Visit Manager</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

        <div class="container">


            <!-- BRAND -->

            <a
                class="navbar-brand fw-bold">

                Patient Visit Manager

            </a>
        </div>
    </nav>

    <div
        class="container d-flex
                justify-content-center
                align-items-center"
        style="min-height: 100vh;">


        <div
            class="card shadow-sm border-0"
            style="width: 400px;">

            <div class="card-body p-4">


                <h3 class="text-center fw-bold mb-2">

                    Patient Visit Manager

                </h3>


                <p class="text-center text-muted mb-4">

                    Login to continue

                </p>


                <?php if ($error !== ""): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <form
                    action="login_process.php"
                    method="POST">


                    <!-- USERNAME -->

                    <div class="mb-3">

                        <label
                            for="username"
                            class="form-label">

                            Username

                        </label>


                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            placeholder="Enter username"
                            required>

                    </div>


                    <!-- PASSWORD -->

                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label">

                            Password

                        </label>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter password"
                            required>

                    </div>


                    <!-- LOGIN BUTTON -->

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Login

                    </button>


                </form>

            </div>

        </div>

    </div>


</body>

</html>

<?php require_once "../includes/footer.php"; ?>