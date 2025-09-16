<?php
session_start();
include '../config/db.php';

// Only allow faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// Check if submission_id is provided
if(!isset($_GET['submission_id'])){
    die("Submission ID is required.");
}

$submission_id = $_GET['submission_id'];

// Fetch submission details
$stmt = $conn->prepare("
    SELECT s.id AS submission_id, s.grade, s.file_path, s.submitted_at,
           a.title AS assignment_title, c.course_name, u.name AS student_name
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN courses c ON a.course_id = c.course_id
    JOIN users u ON s.student_id = u.id
    WHERE s.id = :submission_id
");
$stmt->execute(['submission_id' => $submission_id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$submission){
    die("Submission not found.");
}

// Handle form submission
if(isset($_POST['submit'])){
    $grade = $_POST['grade'];
    $stmt = $conn->prepare("UPDATE submissions SET grade = ? WHERE id = ?");
    if($stmt->execute([$grade, $submission_id])){
        header("Location: view_submissions.php");
        exit;
    } else {
        echo "Error updating grade.";
    }
}
?>

<h2>Grade Submission</h2>
<p><strong>Student:</strong> <?= htmlspecialchars($submission['student_name']) ?></p>
<p><strong>Course:</strong> <?= htmlspecialchars($submission['course_name']) ?></p>
<p><strong>Assignment:</strong> <?= htmlspecialchars($submission['assignment_title']) ?></p>
<p><strong>Submitted At:</strong> <?= htmlspecialchars($submission['submitted_at']) ?></p>
<p>
    <strong>File:</strong> 
    <?php if($submission['file_path']): ?>
        <a href="../uploads/<?= htmlspecialchars($submission['file_path']) ?>" target="_blank">Download</a>
    <?php else: ?>
        No file uploaded
    <?php endif; ?>
</p>

<form method="POST">
    <label>Grade:</label>
    <input type="text" name="grade" value="<?= htmlspecialchars($submission['grade'] ?? '') ?>" required>
    <button type="submit" name="submit">Submit Grade</button>
</form>
