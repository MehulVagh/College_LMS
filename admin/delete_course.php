<?php
include '../config/db.php';

// Check if course_id is provided
if(!isset($_GET['id'])){
    die("Course ID is required.");
}
$course_id = $_GET['id'];

// Delete the course
$stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
if($stmt->execute([$course_id])){
    header("Location: manage_courses.php");
    exit;
} else {
    echo "Error deleting course.";
}
