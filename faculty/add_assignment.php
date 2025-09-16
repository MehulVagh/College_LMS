<?php
session_start();
include '../config/db.php';

// Only allow faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// Get faculty ID
$faculty_id = $_SESSION['user_id'];

// Fetch courses assigned to this faculty
$stmt = $conn->prepare("SELECT * FROM courses WHERE faculty_id = ?");
$stmt->execute([$faculty_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no courses assigned, fetch all courses as fallback
if(empty($courses)){
    $stmt = $conn->prepare("SELECT * FROM courses");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submit
$message = "";
if(isset($_POST['submit'])){
    $course_id = $_POST['course_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $file = null;

    if(!$course_id){
        $message = "❌ Please select a course!";
    } else {
        // File upload
        if(!empty($_FILES['file']['name'])){
            $file = time() . "_" . basename($_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/" . $file);
        }

        $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, due_date, file) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $title, $description, $due_date, $file]);

        $message = "✅ Assignment added successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Assignment - Faculty</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    min-height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
    color: #fff;
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

.container { padding: 2rem; max-width: 800px; }
.page-title {
    text-align: center; margin-bottom: 2rem;
    font-size: 1.8rem; font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}
.form-box {
    background: rgba(255,255,255,0.05);
    padding: 2rem; border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
.form-label { font-weight: 500; color: #e0f7fa; }
.form-control, .form-select {
    background: rgba(255,255,255,0.1);
    border: 1px solid #00ffff; color: #fff;
}
.form-control:focus, .form-select:focus {
    background: rgba(255,255,255,0.15);
    border-color: #3b82f6;
    box-shadow: 0 0 8px rgba(59,130,246,0.7);
}
.submit-btn {
    background: linear-gradient(90deg, #10b981, #059669);
    border: none; padding: 10px 20px;
    border-radius: 6px; color: #fff;
    font-weight: 500; transition: 0.3s;
}
.submit-btn:hover { opacity: 0.9; }
.message {
    text-align: center; margin-bottom: 1rem;
    font-weight: bold; color: #fbbf24;
}
</style>
</head>
<body>
<header>
    <h1>📑 Add Assignment</h1>
    <div class="header-actions">
       
        <a href="../auth/logout.php" class="btn-custom logout-btn">🚪 Logout</a>
    </div>
</header>

<div class="container">
    <h2 class="page-title">Create New Assignment</h2>

    <?php if($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if(empty($courses)): ?>
        <p class="message" style="color:red;">No courses available. Please add a course first.</p>
    <?php else: ?>
    <div class="form-box">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Course *</label>
                <select name="course_id" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['course_id']; ?>"><?= htmlspecialchars($c['course_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Due Date *</label>
                <input type="date" name="due_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">File</label>
                <input type="file" name="file" class="form-control">
            </div>

            <button type="submit" name="submit" class="submit-btn">➕ Add Assignment</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
