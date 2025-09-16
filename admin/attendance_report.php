<?php
session_start();
include '../config/db.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch attendance report
$stmt = $conn->query("
    SELECT u.name as student, c.course_name, 
           COUNT(CASE WHEN a.status='Present' THEN 1 END) as presents, 
           COUNT(*) as total_classes
    FROM attendance a
    JOIN users u ON a.student_id = u.id
    JOIN courses c ON a.course_id = c.course_id
    GROUP BY a.student_id, a.course_id
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Report - Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

.container {
    padding: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}
.page-title {
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

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

/* Percentage badges */
.percent {
    font-weight: 600;
}

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
    <h1>📊 Attendance Report</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
    <h2 class="page-title">Student Attendance</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Present</th>
                    <th>Total</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($data) > 0): ?>
                    <?php foreach($data as $row): 
                        $percent = ($row['total_classes'] > 0) ? round(($row['presents'] / $row['total_classes']) * 100, 2) : 0;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= $row['presents'] ?></td>
                            <td><?= $row['total_classes'] ?></td>
                            <td class="percent"><?= $percent ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="no-data">No attendance records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
