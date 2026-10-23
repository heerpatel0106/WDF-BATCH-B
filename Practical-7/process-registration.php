<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentName = trim($_POST['student_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $clubName    = trim($_POST['club_name'] ?? '');

    $errors = [];

    // Server-Side Validation
    if (empty($studentName)) {
        $errors[] = "Student name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($clubName)) {
        $errors[] = "Please select a club.";
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "messages" => $errors]);
        exit;
    }

    $newRegistration = [
        "id" => uniqid(),
        "student_name" => htmlspecialchars($studentName),
        "email" => htmlspecialchars($email),
        "club_name" => htmlspecialchars($clubName),
        "date" => date("Y-m-d H:i:s")
    ];

    // Save to JSON Storage inside a 'data' folder
    $dir = 'data';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $file = $dir . '/registrations.json';
    $currentData = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $currentData[] = $newRegistration;

    if (file_put_contents($file, json_encode($currentData, JSON_PRETTY_PRINT))) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Successfully registered for " . $clubName]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to save registration data."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}
?>