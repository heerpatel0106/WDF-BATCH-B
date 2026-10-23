<?php
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['user_email']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// 2. Session Timeout implementation (e.g., 15 minutes of inactivity)
$timeout_duration = 900; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time(); // update last activity time stamp
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Student Dashboard</title></head>
<body style="font-family: Arial; margin: 50px;">
    <h2>Welcome to your Student Dashboard, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h2>
    <p>This page is securely protected using PHP Sessions.</p>
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>