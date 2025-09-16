<?php
include '../config/db.php';

// Check if course_id is provided
if(!isset($_GET['id'])){
    die("Course ID is required.");
}
$course_id = $_GET['id'];

// 1️⃣ Delete student enrollments for this course
$stmt = $conn->prepare("DELETE FROM student_courses WHERE course_id = ?");
$stmt->execute([$course_id]);

// 2️⃣ Delete assignments for this course
$stmt = $conn->prepare("DELETE FROM assignments WHERE course_id = ?");
$stmt->execute([$course_id]);

// 3️⃣ Now delete the course itself
$stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
if($stmt->execute([$course_id])){
    header("Location: manage_courses.php");
    exit;
} else {
    echo "Error deleting course.";
}
?>
