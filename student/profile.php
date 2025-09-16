<?php
session_start();
include '../config/db.php';

// Check if student is logged in
if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

// Fetch student details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, email, role, status FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
$msg = '';
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Optional password change
    $params = [$name, $email, $user_id];
    $password_sql = '';
    if(!empty($_POST['password'])){
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password = ?";
        $params = [$name, $email, $hashed, $user_id];
    }

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? $password_sql WHERE id = ?");
    if($stmt->execute($params)){
        $msg = "<div class='alert alert-success'>Profile updated successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error updating profile.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Profile</title>
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

.container { padding: 2rem; max-width: 600px; margin: auto; }
.page-title { text-align: center; margin-bottom: 2rem; font-size: 1.8rem; font-weight: 600; text-shadow: 1px 1px 3px rgba(0,0,0,0.5); }

.card {
    background: rgba(255,255,255,0.05);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
.card label { font-weight: 500; }
.card input { background: rgba(255,255,255,0.08); border: 1px solid #00ffff; color: #fff; }
.card input::placeholder { color: #e0f7fa; }
.card input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 5px rgba(59,130,246,0.5); }

.btn-update { background: linear-gradient(90deg, #10b981, #059669); color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 500; }
.btn-update:hover { opacity: 0.85; }

.alert { border-radius: 8px; padding: 10px 16px; margin-bottom: 1rem; }

@media(max-width:480px){ .page-title { font-size: 1.4rem; } }
</style>
</head>
<body>
<header>
    <h1>👤 Profile</h1>
    <div class="header-actions">
        <a href="dashboard.php" class="btn-custom profile-btn">🏠 Dashboard</a>
        <a href="../auth/logout.php" class="btn-custom logout-btn">🚪 Logout</a>
    </div>
</header>

<div class="container">
    <h2 class="page-title">Student Profile</h2>

    <?php if($msg) echo $msg; ?>

    <div class="card">
        <form method="POST">
            <div class="mb-3">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Role:</label>
                <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label>Status:</label>
                <input type="text" class="form-control" value="<?= ucfirst($user['status']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label>New Password (leave blank to keep current):</label>
                <input type="password" name="password" class="form-control" placeholder="********">
            </div>

            <button type="submit" name="update" class="btn-update">💾 Update Profile</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
