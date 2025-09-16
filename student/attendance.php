<?php
session_start();
include '../config/db.php';

if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch attendance details
$stmt = $conn->prepare("SELECT c.course_name, a.attendance_date, a.status 
                        FROM attendance a 
                        JOIN courses c ON a.course_id = c.course_id 
                        WHERE a.student_id = ?
                        ORDER BY a.attendance_date DESC");
$stmt->execute([$student_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Attendance summary
$summary_stmt = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(status='Present') as present,
    SUM(status='Absent') as absent
    FROM attendance 
    WHERE student_id = ?");
$summary_stmt->execute([$student_id]);
$summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate percentage
$attendance_percentage = 0;
if ($summary['total'] > 0) {
    $attendance_percentage = round(($summary['present'] / $summary['total']) * 100, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Attendance - Student</title>
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
.summary {
    display: flex;
    justify-content: space-around;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.summary .card {
    background: rgba(255,255,255,0.05);
    padding: 1rem;
    border-radius: 12px;
    width: 150px;
    margin: 0.5rem;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
.summary .card h3 {
    margin: 0.5rem 0 0;
    font-size: 1.4rem;
}
.summary .card p {
    margin: 0;
    font-weight: 500;
    color: #f0f0f0;
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

/* Status badges */
.status-present { color: #10b981; font-weight: 600; }
.status-absent { color: #ef4444; font-weight: 600; }

/* No data message */
.no-data {
    text-align: center;
    font-weight: bold;
    color: #fbbf24;
}

/* Responsive */
@media (max-width: 768px) {
    th, td { padding: 10px 12px; font-size: 0.9rem; }
    .summary .card { width: 120px; }
}
@media (max-width: 480px) {
    th, td { padding: 8px 10px; font-size: 0.8rem; }
    .page-title { font-size: 1.4rem; }
    .summary .card { width: 100px; font-size: 0.9rem; }
}
</style>
</head>
<body>
<header>
    <h1>📊 My Attendance</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
    <h2 class="page-title">Attendance Summary</h2>

    <div class="summary">
        <div class="card">
            <p>Total Classes</p>
            <h3><?= $summary['total'] ?: 0 ?></h3>
        </div>
        <div class="card">
            <p>Present</p>
            <h3><?= $summary['present'] ?: 0 ?></h3>
        </div>
        <div class="card">
            <p>Absent</p>
            <h3><?= $summary['absent'] ?: 0 ?></h3>
        </div>
        <div class="card">
            <p>Attendance %</p>
            <h3><?= $attendance_percentage ?>%</h3>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($data) > 0): ?>
                    <?php foreach($data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td><?= $row['attendance_date'] ?></td>
                            <td class="<?= $row['status']=='Present' ? 'status-present' : 'status-absent' ?>">
                                <?= $row['status'] ?: '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="no-data">No attendance records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
