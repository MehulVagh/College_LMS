<?php
session_start();

// check login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

// check role
if(strtolower($_SESSION['role']) != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faculty Dashboard</title>
</head>
<body>
    <h1>Welcome Faculty!</h1>
    <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
    <p>Role: <?php echo $_SESSION['role']; ?></p>
    <a href="../auth/logout.php">Logout</a>


  

<h2>Faculty Dashboard</h2>
<nav>
    <a href="add_assignment.php">➕ Add Assignment</a> | 
    <a href="manage_assignments.php">📂 Manage Assignments</a> |
    <a href="view_submissions.php">View Submissions</a> |  
    <a href="../auth/logout.php">🚪 Logout</a>
</nav>
<hr>
<p>Welcome, Faculty! Use the menu above to manage your assignments.</p>

</body>
</html>
