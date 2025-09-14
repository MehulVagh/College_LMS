<?php
session_start();
include '../config/db.php';

// Only allow students
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Assuming you have a table `enrollments` linking students and courses
// Example: enrollments(student_id, course_id)
$stmt = $conn->prepare("
    SELECT a.*, c.course_name 
    FROM assignments a
    JOIN courses c ON a.course_id = c.course_id
    JOIN enrollments e ON e.course_id = c.course_id
    WHERE e.student_id = ?
");
$stmt->execute([$student_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Your Assignments</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Course</th>
        <th>Title</th>
        <th>Description</th>
        <th>Due Date</th>
        <th>Action</th>
    </tr>
    <?php foreach($assignments as $a): ?>
    <tr>
        <td><?= $a['course_name']; ?></td>
        <td><?= $a['title']; ?></td>
        <td><?= $a['description']; ?></td>
        <td><?= $a['due_date']; ?></td>
        <td>
            <?php if(!empty($a['file'])): ?>
                <a href="../uploads/<?= $a['file']; ?>" target="_blank">Download</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
