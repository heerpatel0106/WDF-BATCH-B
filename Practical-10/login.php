<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Mock database lookup (In production, query your MySQL users table using PDO)
    // Here we assume a user with a hashed password for demonstration
    $mockHashedPassword = password_hash('secure123', PASSWORD_DEFAULT);
    $mockUserRole = 'student'; // can be 'admin' or 'student'

    if ($email === 'student@campushub.com' && password_verify($password, $mockHashedPassword)) {
        // Prevent Session Fixation attacks
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_email'] = $email;
        $_SESSION['role'] = $mockUserRole;
        $_SESSION['last_activity'] = time();

        // Role-based redirection
        if ($mockUserRole === 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head><title>CampusHub - Login</title></head>
<body style="font-family: Arial; margin: 50px;">
    <h2>CampusHub Secure Login</h2>
    <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="login.php">
        <p>Email: <input type="email" name="email" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <button type="submit">Login</button>
    </form>
</body>
</html>