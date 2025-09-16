<?php
// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db.php';

// ✅ Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = trim($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($name) && !empty($email) && !empty($password)) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $password, $role])) {
            $message = "✅ User added successfully!";
        } else {
            $message = "❌ Failed to add user.";
        }
    } else {
        $message = "⚠️ All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add User - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
      color: #fff;
      display: flex;
      flex-direction: column;
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
    header h1 {
      margin: 0;
      font-size: 1.4rem;
    }
    .logout-btn {
      background: linear-gradient(90deg, #ef4444, #dc2626);
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      color: #fff;
      cursor: pointer;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .container {
      flex: 1;
      padding: 2rem;
      max-width: 600px;
      margin: 0 auto;
    }
    .page-title {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.6rem;
    }
    .form-box {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      margin-bottom: 1rem;
      font-size: 1rem;
    }
    .btn {
      display: inline-block;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 0.95rem;
      cursor: pointer;
      transition: 0.3s;
      border: none;
    }
    .submit-btn {
      background: linear-gradient(90deg, #10b981, #059669);
      color: #fff;
    }
    .submit-btn:hover {
      opacity: 0.9;
    }
    .back-btn {
      background: linear-gradient(90deg, #3b82f6, #2563eb);
      color: #fff;
      margin-left: 8px;
    }
    .back-btn:hover {
      opacity: 0.9;
    }
    .message {
      text-align: center;
      margin-bottom: 1rem;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <h1>➕ Add User</h1>
    
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
  </header>

  <div class="container">
    <h2 class="page-title">Create New User</h2>

    <?php if ($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="form-box">
      <form method="POST" action="">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="role">Role</label>
        <select name="role" id="role" required>
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="admin">Admin</option>
        </select>

        <button type="submit" class="btn submit-btn">✅ Add User</button>
        <a href="manage_users.php" class="btn back-btn">⬅ Back</a>
      </form>
    </div>
    
  </div>
</body>
</html>
