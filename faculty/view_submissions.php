<?php
session_start();
include '../config/db.php';

// Only allow faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// Fetch submissions for assignments by this faculty
$faculty_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT s.id, s.file_path, s.submitted_at, u.name AS student_name, 
           a.title AS assignment_title, c.course_name
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    JOIN assignments a ON s.assignment_id = a.id
    JOIN courses c ON a.course_id = c.course_id
    WHERE c.faculty_id = ?
");
$stmt->execute([$faculty_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Assignment Submissions</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Student</th>
        <th>Course</th>
        <th>Assignment</th>
        <th>File</th>
        <th>Submitted At</th>
    </tr>
    <?php foreach($submissions as $s): ?>
    <tr>
        <td><?= $s['student_name']; ?></td>
        <td><?= $s['course_name']; ?></td>
        <td><?= $s['assignment_title']; ?></td>
        <td>
            <?php if(!empty($s['file_path'])): ?>
                <a href="../uploads/<?= $s['file_path']; ?>" target="_blank">Download</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
        <td><?= $s['submitted_at']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
