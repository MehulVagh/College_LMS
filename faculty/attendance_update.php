<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'faculty') exit;

$faculty_id = $_SESSION['user_id'];
$student_id = $_POST['student_id'];
$status = $_POST['status'];
$date = $_POST['date'];
$course_id = $_POST['course_id'];

$stmt = $conn->prepare("
    INSERT INTO attendance (course_id, faculty_id, student_id, attendance_date, status)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE status=VALUES(status)
");
$stmt->execute([$course_id, $faculty_id, $student_id, $date, $status]);
echo 'success';
