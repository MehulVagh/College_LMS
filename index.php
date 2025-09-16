<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - LMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
      color: #fff;
      display: flex;
      height: 100vh;
      overflow: hidden;
    }
    .sidebar {
      width: 250px;
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(12px);
      padding: 2rem 1rem;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 1.5rem;
      margin-bottom: 2rem;
    }
    .sidebar a {
      color: #d1d5db;
      text-decoration: none;
      padding: 0.8rem 1rem;
      border-radius: 8px;
      display: block;
      margin-bottom: 0.5rem;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background: rgba(255,255,255,0.15);
      color: #fff;
    }
    .main {
      flex: 1;
      padding: 2rem;
      overflow-y: auto;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    .header h1 {
      font-size: 1.8rem;
    }
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    .card {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      padding: 1.5rem;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }
    .card h3 {
      margin: 0;
      font-size: 1.2rem;
      color: #e0e7ff;
    }
    .card p {
      font-size: 2rem;
      font-weight: bold;
      margin: 0.5rem 0 0;
    }
    .table {
      background: rgba(255,255,255,0.08);
      border-radius: 12px;
      padding: 1rem;
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      color: #fff;
      font-size: 0.9rem;
    }
    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
    }
    th {
      background: rgba(255,255,255,0.15);
    }
    tr:nth-child(even) {
      background: rgba(255,255,255,0.05);
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#">📊 Dashboard</a>
    <a href="#">👥 Manage Users</a>
    <a href="#">📚 Courses</a>
    <a href="#">📑 Reports</a>
    <a href="../auth/logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="header">
      <h1>Welcome, Admin</h1>
      <p><?= date("l, F j, Y"); ?></p>
    </div>

    <!-- Stats Section -->
    <div class="stats">
      <div class="card">
        <h3>Total Students</h3>
        <p>1,250</p>
      </div>
      <div class="card">
        <h3>Total Faculty</h3>
        <p>85</p>
      </div>
      <div class="card">
        <h3>Active Courses</h3>
        <p>42</p>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="table">
      <h3>Recent User Registrations</h3>
      <table>
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>John Doe</td>
            <td>john@example.com</td>
            <td>Student</td>
            <td>2025-09-01</td>
          </tr>
          <tr>
            <td>Jane Smith</td>
            <td>jane@example.com</td>
            <td>Faculty</td>
            <td>2025-09-10</td>
          </tr>
          <tr>
            <td>Michael Lee</td>
            <td>mike@example.com</td>
            <td>Student</td>
            <td>2025-09-12</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
