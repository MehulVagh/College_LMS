<?php
session_start();
include '../config/db.php';

// Only allow faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if(isset($_POST['submission_id'], $_POST['grade'])){
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];

    // Update grade in submissions table
    $stmt = $conn->prepare("UPDATE submissions SET grade = :grade WHERE id = :submission_id");
    $result = $stmt->execute([
        ':grade' => $grade,
        ':submission_id' => $submission_id
    ]);

    if($result){
        echo "✅ Grade updated!";
    } else {
        http_response_code(500);
        echo "Error updating grade.";
    }
} else {
    http_response_code(400);
    echo "Invalid data.";
}
