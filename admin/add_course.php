<?php
session_start();
include '../config/db.php';

// Only allow admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// Handle form submission
if(isset($_POST['submit'])){
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $description = trim($_POST['description']);

    if(empty($course_name) || empty($course_code)){
        $error = "⚠️ Course Name and Course Code are required!";
    } else {
        // Check for duplicate course code
        $stmt = $conn->prepare("SELECT * FROM courses WHERE course_code = ?");
        $stmt->execute([$course_code]);
        if($stmt->rowCount() > 0){
            $error = "⚠️ Course Code already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (course_name, course_code, description) VALUES (?, ?, ?)");
            if($stmt->execute([$course_name, $course_code, $description])){
                $success = "✅ Course added successfully!";
            } else {
                $error = "❌ Failed to add course!";
            }
        }
    }
}

// Fetch all courses for table
$stmt = $conn->prepare("SELECT * FROM courses ORDER BY course_name ASC");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Course</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
    color: #fff;
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
.logout-btn {
    background: linear-gradient(90deg, #ef4444, #dc2626);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
}
.container {
    padding: 2rem;
    max-width: 900px;
    margin: 0 auto;
}
.welcome {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1.8rem;
    font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}
.card {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    padding: 2rem;
    color: #fff;
    margin-bottom: 2rem;
}
.form-label {
    color: #e0f7fa;
}
.form-control, textarea {
    background: rgba(255,255,255,0.05);
    border: 1px solid #00ffff;
    color: #e0f7fa;
}
.form-control:focus, textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 5px rgba(59,130,246,0.5);
    background: rgba(255,255,255,0.08);
    color: #fff;
}
.submit-btn {
    background: linear-gradient(90deg, #10b981, #059669);
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.submit-btn:hover { opacity: 0.9; }
.message {
    text-align: center;
    margin-bottom: 1rem;
    font-weight: 600;
    color: #fbbf24;
}
/* Table styling */
.table-wrapper {
    background: rgba(255,255,255,0.05);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    color: #fff;
    min-width: 600px;
}
thead {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    color: #fff;
}
th, td {
    padding: 12px 16px;
    text-align: left;
    border: 1px solid rgba(255,255,255,0.3);
}
tbody tr:nth-child(even) { background: rgba(255,255,255,0.05); }
tbody tr:hover { background: rgba(59,130,246,0.3); transition: 0.3s; }
td { color: #e0f7fa; font-weight: 500; }
.back-btn {
    display: inline-block;
    margin-top: 1rem;
    color: #fff;
    text-decoration: none;
    border: 1px solid #fff;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
}
.back-btn:hover { background: rgba(255,255,255,0.1); }
@media (max-width: 768px){ th, td { padding: 10px 12px; font-size: 0.9rem; } }
@media (max-width: 480px){ th, td { padding: 8px 10px; font-size: 0.8rem; } .welcome { font-size: 1.4rem; } }
</style>
</head>
<body>
<header>
    <h1>🛠 Admin Panel</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
    <p class="welcome">Add New Course</p>

    <?php if(isset($error)): ?>
        <div class="message"><?= $error ?></div>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <div class="message"><?= $success ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post">
            <div class="mb-3">
                <label class="form-label" for="course_name">Course Name</label>
                <input type="text" class="form-control" id="course_name" name="course_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="course_code">Course Code</label>
                <input type="text" class="form-control" id="course_code" name="course_code" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" name="submit" class="submit-btn">Add Course</button>
        </form>
    </div>

    <p class="welcome">All Courses</p>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($courses) > 0): ?>
                    <?php foreach($courses as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['course_name']); ?></td>
                            <td><?= htmlspecialchars($c['course_code']); ?></td>
                            <td><?= htmlspecialchars($c['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">No courses available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
