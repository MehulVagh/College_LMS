<?php
session_start();
include '../config/db.php';

// Ensure only students can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

// Fetch assignments with course name
$stmt = $conn->prepare("
    SELECT a.*, c.course_name 
    FROM assignments a 
    LEFT JOIN courses c ON a.course_id = c.course_id
    ORDER BY a.due_date ASC
");
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Assignments</h2>

<?php if(empty($assignments)): ?>
    <p>No assignments available.</p>
<?php else: ?>
<table border="1" cellpadding="10">
<tr>
    <th>Title</th>
    <th>Course</th>
    <th>Description</th>
    <th>Due Date</th>
    <th>File</th>
</tr>
<?php foreach($assignments as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['title']); ?></td>
    <td><?= htmlspecialchars($a['course_name']); ?></td>
    <td><?= htmlspecialchars($a['description']); ?></td>
    <td><?= htmlspecialchars($a['due_date']); ?></td>
    <td>
        <?php if(!empty($a['file'])): ?>
            <a href="../uploads/<?= htmlspecialchars($a['file']); ?>" target="_blank">Download</a>
        <?php else: ?>
            No file
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
