<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'] ?? 1; 
    $clubId = $_POST['club_id'] ?? null;

    if (empty($clubId)) {
        die("Club ID is missing.");
    }

    try {
        // Secure Prepared Statement
        $stmt = $pdo->prepare("INSERT INTO club_registrations (user_id, club_id) VALUES (:user_id, :club_id)");
        
        $stmt->execute([
            'user_id' => $userId,
            'club_id' => $clubId
        ]);

        echo "Successfully registered into the database using PDO Prepared Statements!";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo "You are already registered for this club.";
        } else {
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>