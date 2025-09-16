<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Fetch courses with faculty name
$stmt = $conn->prepare("
    SELECT courses.*, users.name as faculty_name 
    FROM courses 
    LEFT JOIN users ON courses.faculty_id = users.id
    ORDER BY courses.course_id DESC
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Courses - Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Body & Header */
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
.logout-btn {
    background: linear-gradient(90deg, #ef4444, #dc2626);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}
.logout-btn:hover { opacity: 0.9; }

/* Container & Page Title */
.container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
.page-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1.8rem;
    font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}
.action-bar {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1rem;
}
.add-btn {
    background: linear-gradient(90deg, #10b981, #059669);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}
.add-btn:hover { opacity: 0.9; }

/* Table Wrapper */
.table-wrapper {
    background: rgba(255,255,255,0.05);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
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
    border: 1px solid #00ffff;
}
td { color: #e0f7fa; font-weight: 500; }
tbody tr { background: rgba(255,255,255,0.08); transition: 0.3s; }
tbody tr:hover { background: rgba(0, 255, 255, 0.15); }

/* Action buttons */
.actions a {
    margin-right: 8px;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: 0.3s;
}
.edit {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    color: #fff;
}
.edit:hover { opacity: 0.85; }
.delete {
    background: linear-gradient(90deg, #ef4444, #dc2626);
    color: #fff;
}
.delete:hover { opacity: 0.85; }

/* No data message */
.no-data {
    text-align: center;
    font-weight: bold;
    color: #fbbf24;
}

/* Responsive */
@media (max-width: 768px) {
    th, td { padding: 10px 12px; font-size: 0.9rem; }
}
@media (max-width: 480px) {
    th, td { padding: 8px 10px; font-size: 0.8rem; }
    .page-title { font-size: 1.4rem; }
}
</style>
</head>
<body>
<header>
  <h1>📚 Manage Courses</h1>
  <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
  <h2 class="page-title">Course Management</h2>
  <div class="action-bar">
    <a href="add_course.php" class="add-btn">➕ Add Course</a>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Course Name</th>
          <th>Course Code</th>
          <th>Faculty</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(count($courses) > 0): ?>
          <?php foreach($courses as $course): ?>
            <tr>
              <td><?= $course['course_id'] ?></td>
              <td><?= htmlspecialchars($course['course_name']) ?></td>
              <td><?= htmlspecialchars($course['course_code']) ?></td>
              <td><?= htmlspecialchars($course['faculty_name'] ?? '-') ?></td>
              <td class="actions">
                <a href="edit_course.php?id=<?= $course['course_id'] ?>" class="edit">✏️ Edit</a>
                <a href="delete_course.php?id=<?= $course['course_id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this course?')">🗑️ Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="no-data">No courses found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
