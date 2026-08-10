<?php

session_start();

$old = $_SESSION['register_old'] ?? [];

// ?? means If the value on the left exists and is not null, use it. Otherwise, use the value on the right.

$error = $_SESSION['register_error'] ?? '';
$success = $_SESSION['register_success'] ?? '';


unset($_SESSION['register_old']);
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);

// Get theme from cookie
$theme = $_COOKIE['user_theme'] ?? ($old['theme'] ?? 'light');

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            transition: background-color 0.5s ease;
        }

        body.theme-light {
            background-color: #f8f9fa;
        }

        body.theme-dark {
            background-color: #212529;
        }

        body.theme-warm {
            background-color: #fff3cd;
        }

        .card {
            border-radius: 20px;
        }
    </style>

</head>

<body class="theme-<?= htmlspecialchars($theme) ?>">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-md-6 col-lg-5">

                <div class="card shadow">

                    <div class="card-body p-4">

                        <h2 class="text-center mb-4">
                            Create Account
                        </h2>

                        <!-- Display Error -->
                        <?php if ($error != ""): ?>

                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error) ?>
                            </div>

                        <?php endif; ?>

                        <!-- Display Success -->

                        <?php if ($success != ""): ?>

                            <!-- Countdown Progress Bar -->
                            <div class="progress mb-3" style="height: 6px;">
                                <div
                                    class="progress-bar"
                                    role="progressbar"
                                    style="width: 100%; animation: countdownBar 3s linear forwards;">
                                </div>
                            </div>


                            <!-- Success Message -->
                            <div class="alert alert-success text-center" role="alert">

                                <div class="fw-bold">
                                    <?= htmlspecialchars($success) ?>
                                </div>

                                <small>
                                    Redirecting to login in
                                    <span id="countdown">3</span> seconds...
                                </small>

                            </div>


                            <!-- Smooth Countdown Animation -->
                            <style>
                                @keyframes countdownBar {

                                    from {
                                        width: 100%;
                                    }

                                    to {
                                        width: 0%;
                                    }

                                }
                            </style>


                            <!-- Redirect Timer -->
                            <script>
                                let seconds = 3;

                                const countdown =
                                    document.getElementById("countdown");

                                const timer = setInterval(function() {

                                    seconds--;

                                    countdown.textContent = seconds;

                                    if (seconds <= 0) {

                                        clearInterval(timer);

                                        window.location.href = "login.php";

                                    }

                                }, 1000);
                            </script>

                        <?php endif; ?>

                        <form action="register_process.php" method="POST">

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    Username
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    placeholder="Enter username"
                                    value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                                    required>
                                <!-- in value if the username is valid but the password is wrong, the username remains. -->
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="Enter email"
                                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                    required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="Enter password"
                                    required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="confirm_password"
                                    name="confirm_password"
                                    placeholder="Re-enter password"
                                    required>
                            </div>

                            <!-- Theme Preference -->
                            <div class="mb-3">
                                <label class="form-label">Choose your theme</label>

                                <div class="row g-2">

                                    <div class="col-4">
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="theme"
                                            id="lightTheme"
                                            value="light"
                                            <?= $theme === 'light' ? 'checked' : '' ?>>

                                        <label
                                            class="btn btn-outline-primary w-100 h-55 p-2"
                                            for="lightTheme">
                                            <strong>Light</strong>
                                        </label>
                                    </div>

                                    <div class="col-4">
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="theme"
                                            id="darkTheme"
                                            value="dark"
                                            <?= $theme === 'dark' ? 'checked' : '' ?>>

                                        <label
                                            class="btn btn-outline-dark w-100 h-55 p-2"
                                            for="darkTheme">
                                            <strong>Dark</strong>
                                        </label>
                                    </div>

                                    <div class="col-4">
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="theme"
                                            id="warmTheme"
                                            value="warm"
                                            <?= $theme === 'warm' ? 'checked' : '' ?>>

                                        <label
                                            class="btn btn-outline-warning w-100 h-55 p-2"
                                            for="warmTheme">
                                            <strong>Warm</strong>
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <!-- Submit -->
                            <button
                                type="submit"
                                class="btn btn-primary w-100">
                                Register
                            </button>

                            <div class="text-center mt-3">

                                <span class="text-muted">
                                    Already have an account?
                                </span>

                                <a href="login.php" class="text-decoration-none">
                                    Login here
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        const themeOptions = document.querySelectorAll('input[name="theme"]');
        // querySelectorAll Find all elements matching this CSS selector.

        function applyTheme(theme) {
            document.body.classList.remove(
                'theme-light',
                'theme-dark',
                'theme-warm'
            );
            // we dont want <body class="theme-dark theme-warm">
            // we want <body class="theme-warm"> so we clean up the previous theme
            document.body.classList.add('theme-' + theme);
            // then add selected theme
        }

        // Going through all three radio buttons
        themeOptions.forEach(function(option) {

            // option represents the current radio button

            option.addEventListener('change', function() {

                // we use change because the selected radio button changes when the user chooses another theme. 

                if (this.checked) {
                    applyTheme(this.value);
                }

            });

        });

        // Apply selected theme when page loads
        applyTheme("<?= htmlspecialchars($theme) ?>");
    </script>

</body>

</html>