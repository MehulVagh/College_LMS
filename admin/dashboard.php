<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}
include '../config/db.php';
?>

<h1>Admin Dashboard</h1>
<p>Welcome, Admin!</p>

<a href="manage_users.php">Manage Users</a> | 
<a href="manage_courses.php">Manage Courses</a> | 
<a href="../auth/logout.php">Logout</a>
