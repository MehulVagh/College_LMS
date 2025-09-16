<?php
session_start();
if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// Fetch student submissions (optional, for grades table)
$student_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT a.title AS assignment_title, c.course_name, s.submitted_at, s.grade
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    JOIN courses c ON a.course_id = c.course_id
    WHERE s.student_id = ?
    ORDER BY s.submitted_at DESC
");
$stmt->execute([$student_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:"Segoe UI",sans-serif; min-height:100vh; background:linear-gradient(135deg,#1e1b4b,#6d28d9,#0f172a); color:#fff; display:flex; flex-direction:column;}
header { background:rgba(255,255,255,0.08); backdrop-filter:blur(10px); padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,0.3); flex-wrap:wrap;}
header h1 { margin:0; font-size:1.6rem; flex:1 1 auto; }
.logout-btn { background:linear-gradient(90deg,#ef4444,#dc2626); border:none; padding:8px 16px; border-radius:6px; color:#fff; text-decoration:none; font-size:0.9rem; margin-top:0.5rem;}
.container { flex:1; padding:2rem; max-width:1000px; margin:0 auto; }
.welcome { text-align:center; margin-bottom:2rem; font-size:1.6rem;}
.links { display:flex; flex-wrap:wrap; justify-content:center; gap:15px; margin-bottom:2rem;}
.links a { padding:15px 25px; background:rgba(255,255,255,0.08); border-radius:8px; text-decoration:none; color:#fff; font-weight:600; transition:0.3s; text-align:center; min-width:170px;}
.links a:hover { background:linear-gradient(90deg,#3b82f6,#2563eb);}
@media(max-width:768px){ header{flex-direction:column;align-items:flex-start;} .links{flex-direction:column;align-items:center;} }
</style>
</head>
<body>
<header>
<h1>🎓 Welcome, Student!</h1>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
    <a href="profile.php" class="logout-btn" style="background:linear-gradient(90deg,#3b82f6,#2563eb);">👤 Profile</a>
    <a href="../auth/logout.php" class="logout-btn">🚪 Logout</a>
</div>
</header>

<div class="container">
<p class="welcome">Student Dashboard</p>
<div class="links">
  <a href="assignments.php">📘 View Assignments</a>
  <a href="submit_assignment.php">📝 Submit Assignment</a>
  <a href="grades.php">🎯 View Grades</a>
  <a href="attendance.php">✅ View Attendance</a>
</div>
</div>
</body>
</html>
