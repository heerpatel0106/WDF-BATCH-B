<?php
$conn = new mysqli("localhost", "root", "", "campushub_db");

$successMsg = $errorMsg = "";

// Handle Event Creation with Poster File Validation
if (isset($_POST['add_event'])) {
    $title = trim($_POST['title']);
    $date = $_POST['date'];
    
    // File Upload Configuration & Validation
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["poster"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $fileSize = $_FILES["poster"]["size"];

    $allowedTypes = array('jpg', 'jpeg', 'png', 'webp');
    $maxFileSize = 2 * 1024 * 1024; // 2 MB limit

    if (in_array($fileType, $allowedTypes)) {
        if ($fileSize <= $maxFileSize) {
            if (move_uploaded_file($_FILES["poster"]["tmp_name"], $targetFilePath)) {
                // Insert into DB
                $stmt = $conn->prepare("INSERT INTO events_crud (title, event_date, poster_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $date, $targetFilePath);
                if ($stmt->execute()) {
                    $successMsg = "Event and poster uploaded successfully!";
                } else {
                    $errorMsg = "Database error during insertion.";
                }
                $stmt->close();
            } else {
                $errorMsg = "Failed to upload the file.";
            }
        } else {
            $errorMsg = "File size exceeds the 2MB limit.";
        }
    } else {
        $errorMsg = "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed.";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Optional: unlink file from server folder
    $res = $conn->query("SELECT poster_path FROM events_crud WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if (file_exists($row['poster_path'])) unlink($row['poster_path']);
    }
    $conn->query("DELETE FROM events_crud WHERE id = $id");
    header("Location: events-crud.php");
    exit;
}

$events = $conn->query("SELECT * FROM events_crud");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CampusHub - Event Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f6f9; }
        .box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background: #28a745; color: white; }
        input { padding: 8px; margin-right: 10px; }
        button { padding: 8px 15px; background: #28a745; color: white; border: none; cursor: pointer; }
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .msg { color: green; font-weight: bold; }
        .err { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h2>CampusHub - Event Management with Poster Validation (CRUD)</h2>

    <?php if($successMsg) echo "<p class='msg'>$successMsg</p>"; ?>
    <?php if($errorMsg) echo "<p class='err'>$errorMsg</p>"; ?>

    <!-- Add Event Form with File Upload -->
    <div class="box">
        <h3>Create Event & Upload Poster</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Event Title" required>
            <input type="date" name="date" required>
            <input type="file" name="poster" required>
            <button type="submit" name="add_event">Add Event</button>
        </form>
    </div>

    <!-- Events List Table -->
    <div class="box">
        <h3>Managed Events List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Event Title</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php if ($events->num_rows > 0): ?>
                <?php while($row = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><img src="<?= $row['poster_path'] ?>" alt="Poster"></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['event_date'] ?></td>
                    <td>
                        <a href="events-crud.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this event?');" style="color:red; text-decoration:none;">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No events added yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>