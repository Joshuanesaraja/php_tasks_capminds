<?php

session_start();

$old = $_SESSION['register_old'] ?? [];

// ?? means If the value on the left exists and is not null, use it. Otherwise, use the value on the right.

$error = $_SESSION['register_error'] ?? '';

unset($_SESSION['register_old']);
unset($_SESSION['register_error']);

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
</head>

<body class="bg-light">

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
                                            <?= ($old['theme'] ?? 'light') === 'light' ? 'checked' : '' ?>>

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
                                            <?= ($old['theme'] ?? 'light') === 'dark' ? 'checked' : '' ?>>

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
                                            <?= ($old['theme'] ?? 'light') === 'warm' ? 'checked' : '' ?>>

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

</body>

</html>