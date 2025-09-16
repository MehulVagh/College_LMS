<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = $_GET['id'];

// ✅ Check if user is assigned to any courses
$stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE faculty_id = ?");
$stmt->execute([$id]);
$courseCount = $stmt->fetchColumn();

if ($courseCount > 0) {
    // User is assigned to courses, cannot delete
    die("Cannot delete this user because they are assigned to $courseCount course(s). Please reassign or remove the courses first.");
}

// ✅ Safe to delete
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Redirect back to manage users
header("Location: manage_users.php");
exit;
?>
