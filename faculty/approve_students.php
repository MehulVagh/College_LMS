<?php
session_start();
include '../config/db.php';

// Only faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// Approve or reject
if(isset($_GET['action'], $_GET['id'])){
    $id = $_GET['id'];
    $action = $_GET['action'];
    if(in_array($action, ['approved','rejected'])){
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'student'");
        $stmt->execute([$action, $id]);
        $msg = "<div class='alert alert-success'>Student has been ".htmlspecialchars($action)."!</div>";
    }
}

// Fetch pending students
$stmt = $conn->query("SELECT id, name, email FROM users WHERE role='student' AND status='pending'");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Student Registrations</title>
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

/* Container & Title */
.container { padding: 2rem; max-width: 900px; margin: auto; }
.page-title { text-align: center; margin-bottom: 2rem; font-size: 1.8rem; font-weight: 600; text-shadow: 1px 1px 3px rgba(0,0,0,0.5); }

/* Table Styling */
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
thead { background: linear-gradient(90deg, #3b82f6, #2563eb); color: #fff; }
th, td { padding: 12px 16px; text-align: left; border: 1px solid #00ffff; }
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
.approve { background: linear-gradient(90deg, #10b981, #059669); color: #fff; }
.approve:hover { opacity: 0.85; }
.reject { background: linear-gradient(90deg, #ef4444, #dc2626); color: #fff; }
.reject:hover { opacity: 0.85; }

/* No data message */
.no-data { text-align: center; font-weight: bold; color: #fbbf24; }

/* Responsive */
@media (max-width: 768px){ th, td { padding: 10px 12px; font-size: 0.9rem; } }
@media (max-width: 480px){ th, td { padding: 8px 10px; font-size: 0.8rem; } .page-title { font-size: 1.4rem; } }
</style>
</head>
<body>
<header>
    <h1>👥 Pending Student Registrations</h1>
    <div class="header-actions">
        
        <a href="../auth/logout.php" class="btn-custom logout-btn">🚪 Logout</a>
    </div>
</header>

<div class="container">
    <h2 class="page-title">Student Registration Requests</h2>

    <?php if(isset($msg)) echo $msg; ?>

    <div class="table-wrapper">
        <?php if(count($students) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td class="actions">
                            <a href="?id=<?= $s['id'] ?>&action=approved" class="approve">✅ Approve</a>
                            <a href="?id=<?= $s['id'] ?>&action=rejected" class="reject">❌ Reject</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No pending registrations.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
