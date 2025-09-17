<?php
include '../config/db.php';

$message = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = 'student'; 
    $status = 'pending';

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $message = "<div class='error'>Email already registered!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users(name, email, password, role, status) VALUES(?,?,?,?,?)");
        if ($stmt->execute([$name, $email, $password, $role, $status])) {
            $message = "<div class='success'>✅ Registration submitted! Awaiting faculty approval.</div>";
        } else {
            $message = "<div class='error'>❌ Error in registration!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>iLearn Registration</title>
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
      max-width: 420px;
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
    input[type="text"],
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
    .login-link {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.85rem;
    }
    .login-link a {
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
    .success {
      background: rgba(34,197,94,0.2);
      color: #bbf7d0;
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
    <h2>iLearn Registration</h2>
    <p>Create your student account to start learning.</p>

    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" action="">
      <label for="name">Full Name</label>
      <input type="text" name="name" placeholder="John Doe" required>

      <label for="email">Email</label>
      <input type="email" name="email" placeholder="student@lms.com" required>

      <label for="password">Password</label>
      <input type="password" name="password" placeholder="••••••••" required>

      <button type="submit" name="register" class="btn">Register</button>

      <div class="login-link">
        Already have an account? <a href="login.php">Sign In</a>
      </div>
    </form>
  </div>
</body>
</html>
