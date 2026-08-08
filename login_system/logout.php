<?php
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect back to login page
header("Location: login.php");

// Stops the script immediately.
exit();

// best alternative for header()

// if (!isset($_SESSION['username'])) {
//     header("Location: login.php");
//     exit();
// }

?>


<!-- option 1: 
------------------------------------------------

<?php
session_start();

session_unset();
session_destroy();

include 'login.php';
exit();

?>


option 2: JavaScript Redirect (Not recommended for server-side redirects.)
------------------------------------------------

<?php
session_start();

session_unset();
session_destroy();
?>

<script>
    window.location.href = "login.php";
</script>

//keeps the current page in browser history.

option 3:
------------------------------------------------

<?php
session_start();

session_unset();
session_destroy();
?>

<meta http-equiv="refresh" content="0;url=login.php">

option 4:  (similar to option 2)
------------------------------------------------

<?php
session_start();

session_unset();
session_destroy();
?>

<script>
    location.replace("login.php");
</script>

//replaces the current page in history, so pressing the Back button won't return to logout.php.

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
    
-->