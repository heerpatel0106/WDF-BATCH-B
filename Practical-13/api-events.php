<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "campushub_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM api_events ORDER BY id DESC");
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $events]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $title = trim($data['title'] ?? '');
        $date = trim($data['date'] ?? '');
        $desc = trim($data['description'] ?? '');

        if (empty($title) || empty($date)) {
            echo json_encode(["status" => "error", "message" => "Title and date are required"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO api_events (title, event_date, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $date, $desc);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Event added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add event"]);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM api_events WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Event deleted"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Deletion failed"]);
            }
            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}
$conn->close();
?>