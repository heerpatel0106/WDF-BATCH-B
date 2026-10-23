<?php
// Connect via MySQLi as requested in the problem definition
$conn = new mysqli("localhost", "root", "", "campushub_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMsg = $errorMsg = "";

// 1. Handle Create / Insert
if (isset($_POST['add_member'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    if (!empty($name) && !empty($email) && !empty($course)) {
        $stmt = $conn->prepare("INSERT INTO members (name, email, course) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $course);
        if ($stmt->execute()) {
            $successMsg = "Member added successfully!";
        } else {
            $errorMsg = "Error: Email might already exist.";
        }
        $stmt->close();
    } else {
        $errorMsg = "All fields are required.";
    }
}

// 2. Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM members WHERE id = $id");
    header("Location: members-crud.php");
    exit;
}

// 3. Handle Search and Filter
$search = $_GET['search'] ?? '';
$filterCourse = $_GET['filter_course'] ?? '';

$sql = "SELECT * FROM members WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($filterCourse)) {
    $sql .= " AND course = '$filterCourse'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CampusHub - Member Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f6f9; }
        .box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        input, select { padding: 8px; margin-right: 10px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .msg { color: green; font-weight: bold; }
        .err { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h2>CampusHub - Club Member Management (CRUD)</h2>
    
    <?php if($successMsg) echo "<p class='msg'>$successMsg</p>"; ?>
    <?php if($errorMsg) echo "<p class='err'>$errorMsg</p>"; ?>

    <!-- Add Member Form -->
    <div class="box">
        <h3>Add New Member</h3>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="course" placeholder="Course / Branch" required>
            <button type="submit" name="add_member">Add Member</button>
        </form>
    </div>

    <!-- Search and Filter Bar -->
    <div class="box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
            <select name="filter_course">
                <option value="">All Courses</option>
                <option value="Computer Engineering" <?= $filterCourse == 'Computer Engineering' ? 'selected' : '' ?>>Computer Engineering</option>
                <option value="IT" <?= $filterCourse == 'IT' ? 'selected' : '' ?>>IT</option>
                <option value="Mechanical" <?= $filterCourse == 'Mechanical' ? 'selected' : '' ?>>Mechanical</option>
            </select>
            <button type="submit">Filter / Search</button>
            <a href="members-crud.php"><button type="button" style="background:#6c757d;">Reset</button></a>
        </form>
    </div>

    <!-- Data Table -->
    <div class="box">
        <h3>Members List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td>
                        <a href="members-crud.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');" style="color:red; text-decoration:none;">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No records found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>