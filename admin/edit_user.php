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

$id = $_GET['id'] ?? null;
if (!$id) {
    die("User ID missing!");
}

// ✅ Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found!");
}

// ✅ Update logic
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, password=? WHERE id=?");
        $stmt->execute([$name, $email, $role, $password, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
        $stmt->execute([$name, $email, $role, $id]);
    }

    header("Location: manage_users.php?msg=User updated successfully");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User - Admin</title>
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
    .logout-btn:hover {
      opacity: 0.9;
    }
    .container {
      flex: 1;
      padding: 2rem;
      max-width: 600px;
      margin: 2rem auto;
      background: rgba(255,255,255,0.08);
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
      backdrop-filter: blur(12px);
    }
    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    label {
      display: block;
      margin-top: 1rem;
      margin-bottom: 0.4rem;
      font-weight: 600;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 8px;
      outline: none;
      background: rgba(255,255,255,0.12);
      color: #fff;
    }
    input::placeholder {
      color: #aaa;
    }
    button {
      margin-top: 1.5rem;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(90deg, #10b981, #059669);
      color: #fff;
      font-size: 1rem;
      cursor: pointer;
    }
    button:hover {
      opacity: 0.9;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 1rem;
      text-decoration: none;
      color: #3b82f6;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <header>
    <h1>✏️ Edit User</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
  </header>

  <div class="container">
    <h2>Edit User Details</h2>
    <form method="POST">
      <label>Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

      <label>Role:</label>
      <select name="role">
        <option value="admin" <?= $user['role']=="admin"?"selected":""; ?>>Admin</option>
        <option value="faculty" <?= $user['role']=="faculty"?"selected":""; ?>>Faculty</option>
        <option value="student" <?= $user['role']=="student"?"selected":""; ?>>Student</option>
      </select>

      <label>Password:</label>
      <input type="password" name="password" placeholder="Leave blank to keep same">

      <button type="submit" name="update">Update User</button>
    </form>
    <a href="manage_users.php" class="back-link">⬅ Back to Manage Users</a>
  </div>
  <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</body>
</html>
