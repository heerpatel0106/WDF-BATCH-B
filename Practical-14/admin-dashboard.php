<?php
session_start();
$conn = new mysqli("localhost", "root", "", "campushub_db");

// 1. Mock login for testing if not already logged in (Removes login friction during testing)
if (!isset($_SESSION['user_email'])) {
    $_SESSION['user_email'] = 'admin@campushub.com';
    $_SESSION['role'] = 'admin'; // Change to 'student' to test access restriction
}

// 2. Role-Based Access Control (RBAC): Restrict to 'admin' only
if ($_SESSION['role'] !== 'admin') {
    die("Access Denied: You do not have administrator privileges to view this portal.");
}

// 3. Record Audit Log for Admin Access
$userEmail = $_SESSION['user_email'];
$actionDesc = "Accessed Admin Dashboard";
$ip = $_SERVER['REMOTE_ADDR'];
$stmtLog = $conn->prepare("INSERT INTO audit_logs (user_email, action, ip_address) VALUES (?, ?, ?)");
$stmtLog->bind_param("sss", $userEmail, $actionDesc, $ip);
$stmtLog->execute();
$stmtLog->close();

// 4. SQL Aggregation for Dashboard Statistics
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM admin_users")->fetch_assoc()['count'];
$totalEvents = $conn->query("SELECT COUNT(*) AS count FROM api_events")->fetch_assoc()['count'];
$totalRegistrations = $conn->query("SELECT COUNT(*) AS count FROM club_registrations")->fetch_assoc()['count'];

// Fetch latest audit logs for traceability
$logsResult = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CampusHub - Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f9; display: flex; }
        /* Sidebar Dynamic Menu */
        .sidebar { width: 250px; background: #343a40; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar h2 { font-size: 20px; color: #ffc107; margin-bottom: 30px; }
        .sidebar a { display: block; color: #cfd8dc; text-decoration: none; padding: 10px 0; border-bottom: 1px solid #4b545c; }
        .sidebar a:hover { color: white; padding-left: 5px; }
        
        /* Main Content */
        .main-content { margin-left: 270px; padding: 30px; width: calc(100% - 270px); }
        .cards-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 5px solid #007bff; }
        .stat-card.events { border-left-color: #28a745; }
        .stat-card.regs { border-left-color: #ffc107; }
        .stat-card h3 { margin: 0; font-size: 14px; color: #6c757d; }
        .stat-card p { font-size: 24px; font-weight: bold; margin: 10px 0 0 0; color: #333; }
        
        .box { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>

    <!-- Dynamic Menu based on Role -->
    <div class="sidebar">
        <h2>CampusHub Admin</h2>
        <a href="admin-dashboard.php">📊 Dashboard Overview</a>
        <a href="async-crud.html">🎉 Manage Events (API)</a>
        <a href="members-crud.php">👥 Club Members CRUD</a>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="#" style="color:#ffc107;">🔒 Admin Audit Logs</a>
        <?php endif; ?>
        <a href="logout.php" style="color: #dc3545; margin-top: 40px; display:block;">🚪 Logout</a>
    </div>

    <!-- Main Dashboard Area -->
    <div class="main-content">
        <h2>Welcome to the Admin Control Panel</h2>
        <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['user_email']) ?></strong> (Role: <em><?= ucfirst($_SESSION['role']) ?></em>)</p>

        <!-- Statistics Cards -->
        <div class="cards-grid">
            <div class="stat-card">
                <h3>Total Portal Users</h3>
                <p><?= $totalUsers ?></p>
            </div>
            <div class="stat-card events">
                <h3>Managed Events</h3>
                <p><?= $totalEvents ?></p>
            </div>
            <div class="stat-card regs">
                <h3>Club Registrations</h3>
                <p><?= $totalRegistrations ?></p>
            </div>
        </div>

        <!-- Audit Log Traceability Section -->
        <div class="box">
            <h3>Recent System Audit Logs</h3>
            <p style="font-size: 13px; color: #6c757d;">Traceability log recording administrative and tracking actions.</p>
            <table>
                <tr>
                    <th>Log ID</th>
                    <th>User Email</th>
                    <th>Action Performed</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
                <?php while($log = $logsResult->fetch_assoc()): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= htmlspecialchars($log['user_email']) ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= $log['ip_address'] ?></td>
                    <td><?= $log['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

</body>
</html>