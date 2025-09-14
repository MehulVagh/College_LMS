<?php
session_start();
include '../config/db.php';

// Only faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// Get assignment ID
if(!isset($_GET['id'])){
    header("Location: manage_assignments.php");
    exit;
}

$id = $_GET['id'];

// Fetch assignment details
$stmt = $conn->prepare("SELECT * FROM assignments WHERE id = ?");
$stmt->execute([$id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch courses for dropdown
$faculty_id = $_SESSION['user_id'];
$stmt2 = $conn->prepare("SELECT * FROM courses WHERE faculty_id = ?");
$stmt2->execute([$faculty_id]);
$courses = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if(isset($_POST['update'])){
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE assignments SET course_id = ?, title = ?, description = ?, due_date = ? WHERE id = ?");
    $stmt->execute([$course_id, $title, $description, $due_date, $id]);

    header("Location: manage_assignments.php?msg=Assignment updated successfully");
    exit;
}
?>

<h2>Edit Assignment</h2>
<form method="POST">
    <label>Course:</label>
    <select name="course_id" required>
        <?php foreach($courses as $c): ?>
            <option value="<?= $c['course_id']; ?>" <?= ($c['course_id'] == $assignment['course_id']) ? 'selected' : '' ?>>
                <?= $c['course_name']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Title:</label>
    <input type="text" name="title" value="<?= $assignment['title']; ?>" required><br><br>

    <label>Description:</label>
    <textarea name="description"><?= $assignment['description']; ?></textarea><br><br>

    <label>Due Date:</label>
    <input type="date" name="due_date" value="<?= $assignment['due_date']; ?>" required><br><br>

    <button type="submit" name="update">Update Assignment</button>
</form>
