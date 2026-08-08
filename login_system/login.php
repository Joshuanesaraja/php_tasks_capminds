<!-- everything begins here. -->

<!-- this project does
1. Validate the entered data
2. Authenticate the user
3. Maintain login using Sessions
4. Remember user preferences using Cookies -->

<!-- flow of program
login -> auth -> If validation fails => back to login
else => Authentication -> Compare with dummy users -> if yes => Create Cookie -> dashboard.php ->
Display Data ->  Logout -> Destroy Session -> Back to login.php
else => Back to login.php  
-->

<?php

session_start();

// cookies

// Default values
$username = "";
$theme = "light";

// Check Remember Me cookie
if (isset($_COOKIE['remember_username'])) {
    $username = $_COOKIE['remember_username'];
}

// Check Theme cookie
if (isset($_COOKIE['user_theme'])) {
    $theme = $_COOKIE['user_theme'];
}

// checks succesful registration
$success = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_success']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body.light {
            background-color: #f8f9fa;
            color: black;
        }

        body.dark {
            background-color: #212529;
            color: white;
        }

        body.warm {
            background-color: #ffe5b4;
            color: #5a3d00;
        }

        .card {
            border-radius: 15px;
        }
    </style>

</head>

<body class="<?php echo $theme; ?>">
    <!-- $theme -> if the theme contains dark it changes to dark likewise it changes the theme -->

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow p-4" style="width:400px;">
            <h2 class="text-center mb-4">
                Login
            </h2>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            
            ?>

            <?php if ($success != ""): ?>

                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>


            <form action="auth.php" method="POST">

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter your Name" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email address" required>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-primary w-100">Login</button>

                <div class="text-center mt-3">

                    <span class="text-muted">
                        Don't have an account?
                    </span>

                    <a href="register.php" class="text-decoration-none">
                        Register here
                    </a>

                </div>

            </form>


        </div>
    </div>

</body>

</html>