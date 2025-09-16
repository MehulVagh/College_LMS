<?php
session_start();
include '../config/db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Find user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user['password']) { 
        // store session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($user['role'] == 'faculty') {
            header("Location: ../faculty/dashboard.php");
        } else {
            header("Location: ../student/dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LMS Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #fff;
    }
    .card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 2rem;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
    .card h2 {
      text-align: center;
      margin-bottom: 0.5rem;
      font-size: 1.8rem;
    }
    .card p {
      text-align: center;
      font-size: 0.9rem;
      color: #d1d5db;
      margin-bottom: 1.5rem;
    }
    label {
      font-size: 0.85rem;
      margin-bottom: 0.3rem;
      display: block;
    }
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.7rem;
      border: none;
      border-radius: 8px;
      background: rgba(255,255,255,0.2);
      color: #fff;
      margin-bottom: 1rem;
    }
    input::placeholder {
      color: #cbd5e1;
    }
    .actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.2rem;
      font-size: 0.85rem;
    }
    .actions a {
      color: #93c5fd;
      text-decoration: none;
    }
    .btn {
      width: 100%;
      padding: 0.8rem;
      border: none;
      border-radius: 10px;
      background: linear-gradient(to right, #6366f1, #8b5cf6);
      color: #fff;
      font-size: 1rem;
      cursor: pointer;
      transition: opacity 0.3s;
    }
    .btn:hover {
      opacity: 0.9;
    }
    .signup {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.85rem;
    }
    .signup a {
      color: #fbbf24;
      text-decoration: none;
      font-weight: bold;
    }
    .error {
      background: rgba(239, 68, 68, 0.2);
      color: #fecaca;
      padding: 0.5rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      text-align: center;
      font-size: 0.85rem;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>LearnSphere Login</h2>
    <p>Welcome back! Enter your details to continue your journey.</p>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="email">Email</label>
      <input type="email" name="email" placeholder="student@lms.com" required>

      <label for="password">Password</label>
      <input type="password" name="password" placeholder="••••••••" required>

      <div class="actions">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" name="login" class="btn">Sign In</button>

      <div class="signup">
        Don’t have an account? <a href="register.php">Sign Up</a>
      </div>
    </form>
  </div>
</body>
</html>
