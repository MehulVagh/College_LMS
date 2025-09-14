<?php
session_start();
include '../config/db.php';

// Only faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = $_GET['id'];

    // Delete assignment
    $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: manage_assignments.php?msg=Assignment deleted successfully");
    exit;
} else {
    echo "No assignment ID provided!";
}
?>
