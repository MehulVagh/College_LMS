<?php
session_start();

// Check login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

// Check role
if(strtolower($_SESSION['role']) != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Fetch only graded submissions
$student_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT a.title AS assignment_title, c.course_name, s.submitted_at, s.grade
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN courses c ON a.course_id = c.course_id
    WHERE s.student_id = ? AND s.grade IS NOT NULL
    ORDER BY s.submitted_at DESC
");
$stmt->execute([$student_id]);
$graded_submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Grades</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Body & Header */
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

/* Container & Heading */
.container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
.welcome {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1.8rem;
    font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

/* Table Wrapper */
.table-wrapper {
    background: rgba(255,255,255,0.08);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    overflow-x: auto;
}

/* Table Styling */
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
td {
    color: #e0f7fa;
    font-weight: 500;
}
tbody tr:nth-child(even) {
    background: rgba(255,255,255,0.05);
}
tbody tr:hover {
    background: rgba(59, 130, 246, 0.3);
    transition: 0.3s;
}

/* No Data Message */
.no-data {
    text-align: center;
    font-weight: bold;
    margin-top: 1rem;
    color: #fbbf24;
}

/* Back Button */
.back-btn {
    display: inline-block;
    margin-top: 1.5rem;
    color: #fff;
    text-decoration: none;
    border: 1px solid #fff;
    padding: 8px 16px;
    border-radius: 6px;
    transition: 0.3s;
}
.back-btn:hover {
    background: rgba(255,255,255,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    th, td {
        padding: 10px 12px;
        font-size: 0.9rem;
    }
}
@media (max-width: 480px) {
    th, td {
        padding: 8px 10px;
        font-size: 0.8rem;
    }
    .welcome {
        font-size: 1.4rem;
    }
}
</style>
</head>
<body>
<header>
  <h1>🎓 Welcome, <?= htmlspecialchars($_SESSION['role']); ?>!</h1>
  <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
  <p class="welcome">My Grades</p>

  <?php if(count($graded_submissions) > 0): ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Course</th>
          <th>Assignment</th>
          <th>Submitted At</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($graded_submissions as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['course_name']); ?></td>
          <td><?= htmlspecialchars($s['assignment_title']); ?></td>
          <td><?= htmlspecialchars($s['submitted_at']); ?></td>
          <td><?= htmlspecialchars($s['grade']); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="no-data">No graded assignments yet.</p>
  <?php endif; ?>

  <a href="student_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
