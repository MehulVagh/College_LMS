<?php
session_start();
include '../config/db.php';

// Only allow students
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

// Fetch all assignments with course info
$sql = "SELECT a.id AS assignment_id, a.title, a.description, a.due_date, c.course_name
        FROM assignments a
        JOIN courses c ON a.course_id = c.course_id
        ORDER BY a.due_date ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Assignments</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      min-height: 100vh;
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

    header h1 {
      margin: 0;
      font-size: 1.6rem;
    }

    .logout-btn {
      background: linear-gradient(90deg, #ef4444, #dc2626);
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      color: #fff;
      text-decoration: none;
    }

    .container {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .welcome {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.8rem;
      font-weight: 600;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }

    /* Table styling */
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
    }

    thead {
      background: linear-gradient(90deg, #3b82f6, #2563eb);
      color: #fff;
    }

    th, td {
      padding: 12px 16px;
      text-align: left;
      border: 1px solid #00ffff; /* bright cyan border for contrast */
    }

    tbody tr {
      background: rgba(255,255,255,0.08);
      transition: 0.3s;
    }

    tbody tr:hover {
      background: rgba(0, 255, 255, 0.15); /* bright hover for visibility */
    }

    td {
      color: #e0f7fa; /* bright text inside table */
      font-weight: 500;
    }

    @media (max-width: 768px) {
      th, td {
        padding: 10px 12px;
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      th, td {
        padding: 8px 10px;
        font-size: 0.8rem;
      }
      .welcome {
        font-size: 1.4rem;
      }
    }

  </style>
</head>
<body>
  <header>
    <h1>🎓 Welcome, <?= htmlspecialchars($_SESSION['role']); ?>!</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
  </header>

  <div class="container">
    <p class="welcome">Assignments</p>

    <?php if(count($assignments) > 0): ?>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Course</th>
              <th>Title</th>
              <th>Description</th>
              <th>Due Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($assignments as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['course_name']); ?></td>
              <td><?= htmlspecialchars($a['title']); ?></td>
              <td><?= htmlspecialchars($a['description']); ?></td>
              <td><?= htmlspecialchars($a['due_date']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center fw-bold text-warning mt-3">No assignments available.</p>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
