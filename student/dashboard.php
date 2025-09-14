<?php
session_start();

// check login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

// check role
if(strtolower($_SESSION['role']) != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome Student!</h1>
    <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
    <p>Role: <?php echo $_SESSION['role']; ?></p>
    <a href="../auth/logout.php">Logout</a>
    
<h2>Student Dashboard</h2>
<nav>
    <a href="assignments.php">📘 View Assignments</a> | 
    <a href="submit_assignment.php">Submit Assignment</a> | 
    <a href="../auth/logout.php">🚪 Logout</a>
</nav>
<hr>
<p>Welcome, Student! Check your assignments from the menu above.</p>
</body>
</html>
