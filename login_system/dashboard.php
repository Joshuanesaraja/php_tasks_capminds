<?php
session_start();

// Protect Dashboard
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get session data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$theme = $_SESSION['theme'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

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

<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="card shadow p-4" style="width: 500px;">

        <h2 class="text-center mb-4">
            Welcome, <?php echo htmlspecialchars($username); ?>
        </h2>

        <table class="table table-bordered">

            <tr>
                <th>User ID</th>
                <td><?php echo $user_id; ?></td>
            </tr>

            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($username); ?></td>
            </tr>

            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($email); ?></td>
            </tr>

            <tr>
                <th>Theme</th>
                <td><?php echo ucfirst($theme); ?></td>
            </tr>
        </table>

        <h4 class="mt-4">Session Details</h4>

        <table class="table table-bordered">
            <tr>
                <th>Session Variable</th>
                <th>Value</th>
            </tr>

            <tr>
                <td>$_SESSION['user_id']</td>
                <td><?php echo $_SESSION['user_id']; ?></td>
            </tr>

            <tr>
                <td>$_SESSION['username']</td>
                <td><?php echo $_SESSION['username']; ?></td>
            </tr>

            <tr>
                <td>$_SESSION['email']</td>
                <td><?php echo $_SESSION['email']; ?></td>
            </tr>

            <tr>
                <td>$_SESSION['theme']</td>
                <td><?php echo $_SESSION['theme']; ?></td>
            </tr>
            </table>

        <div class="text-center mt-3">
            <a href="logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>

    </div>

</div>

</body>
</html>