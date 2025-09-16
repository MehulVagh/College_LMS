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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Assignment - Faculty</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
    color: #fff;
    min-height: 100vh;
    margin: 0;
}
header {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
header h1 { margin: 0; font-size: 1.6rem; }
.header-actions { display: flex; gap: 0.75rem; }
.btn-custom {
    border: none; padding: 8px 16px;
    border-radius: 6px; color: #fff;
    text-decoration: none; font-weight: 500;
    transition: 0.3s;
}
.profile-btn { background: linear-gradient(90deg, #3b82f6, #2563eb); }
.profile-btn:hover { opacity: 0.9; }
.logout-btn { background: linear-gradient(90deg, #ef4444, #dc2626); }
.logout-btn:hover { opacity: 0.9; }

.container { padding: 2rem; max-width: 700px; }
.page-title {
    text-align: center; margin-bottom: 2rem;
    font-size: 1.8rem; font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}
.form-wrapper {
    background: rgba(255,255,255,0.05);
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
label { font-weight: 500; }
input, select, textarea, button {
    width: 100%; margin-bottom: 1rem;
    padding: 10px; border-radius: 6px;
    border: 1px solid #00ffff; background: rgba(0,0,0,0.2); color: #fff;
}
button { 
    background: linear-gradient(90deg, #10b981, #059669); 
    border: none; cursor: pointer; font-weight: 500;
    transition: 0.3s;
}
button:hover { opacity: 0.85; }
@media (max-width: 480px) {
    .page-title { font-size: 1.4rem; }
}
</style>
</head>
<body>
<header>
    <h1>✏️ Edit Assignment</h1>
    <div class="header-actions">
        
        <a href="../auth/logout.php" class="btn-custom logout-btn">🚪 Logout</a>
    </div>
</header>

<div class="container">
    <h2 class="page-title">Edit Assignment</h2>
    <div class="form-wrapper">
        <form method="POST">
            <label>Course:</label>
            <select name="course_id" required>
                <option value="">--Select Course--</option>
                <?php foreach($courses as $c): ?>
                    <option value="<?= $c['course_id']; ?>" <?= ($c['course_id'] == $assignment['course_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($assignment['title']); ?>" required>

            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($assignment['description']); ?></textarea>

            <label>Due Date:</label>
            <input type="date" name="due_date" value="<?= $assignment['due_date']; ?>" required>

            <button type="submit" name="update">Update Assignment</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
