<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// ✅ Replace `course_name` below with the actual column from your `courses` table
$stmt = $conn->query("SELECT a.id, a.title, a.description, a.due_date, 
                             c.course_name 
                      FROM assignments a 
                      JOIN courses c ON a.course_id = c.course_id");


$assignments = $stmt->fetchAll();
?>

<h2>Assignments</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Course</th>
        <th>Title</th>
        <th>Due Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach($assignments as $a): ?>
    <tr>
        <td><?= $a['id'] ?></td>
        <td><?= $a['course_name'] ?></td>
        <td><?= $a['title'] ?></td>
        <td><?= $a['due_date'] ?></td>
        <td>
            <a href="edit_assignment.php?id=<?= $a['id'] ?>">Edit</a> | 
            <a href="delete_assignment.php?id=<?= $a['id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
