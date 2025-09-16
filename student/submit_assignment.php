<?php
session_start();
include '../config/db.php';

// Only allow students
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch assignments to submit
$sql = "SELECT a.id AS assignment_id, a.title, a.description, c.course_name
        FROM assignments a
        JOIN courses c ON a.course_id = c.course_id
        ORDER BY a.due_date ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle submission
$message = '';
if(isset($_POST['submit'])){
    $assignment_id = $_POST['assignment_id'];
    $file_path = null;

    if(!empty($_FILES['file']['name'])){
        $file_path = time() . "_" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/" . $file_path);

        // Insert submission
        $stmt2 = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, file_path, submitted_at) VALUES (?, ?, ?, NOW())");
        if($stmt2->execute([$assignment_id, $student_id, $file_path])){
            $message = "✅ Assignment submitted successfully!";
        } else {
            $message = "❌ Submission failed!";
        }
    } else {
        $message = "⚠️ Please select a file to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Assignment</title>
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
      max-width: 700px;
      margin: 0 auto;
    }

    .welcome {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.8rem;
      font-weight: 600;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }

    .card {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
      padding: 2rem;
      color: #fff;
    }

    .form-label {
      color: #e0f7fa;
    }

    .form-control, .form-select {
      background: rgba(255,255,255,0.05);
      border: 1px solid #00ffff;
      color: #e0f7fa;
    }

    .form-control:focus, .form-select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 5px rgba(59,130,246,0.5);
      background: rgba(255,255,255,0.08);
      color: #fff;
    }

    .submit-btn {
      background: linear-gradient(90deg, #10b981, #059669);
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    .submit-btn:hover {
      opacity: 0.9;
    }

    .message {
      text-align: center;
      margin-bottom: 1rem;
      font-weight: 600;
      color: #fbbf24;
    }

    @media (max-width: 768px){
      .container {
        padding: 1rem;
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
    <p class="welcome">Submit Assignment</p>

    <?php if($message): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="assignment_id" class="form-label">Select Assignment</label>
          <select class="form-select" name="assignment_id" id="assignment_id" required>
  <option value="">-- Select Assignment --</option>
  <?php foreach($assignments as $a): ?>
    <option value="<?= $a['assignment_id']; ?>" style="color:#000; background:#fff;">
      <?= htmlspecialchars($a['course_name'] . ' - ' . $a['title']); ?>
    </option>
  <?php endforeach; ?>
</select>
        </div>

        <div class="mb-3">
          <label for="file" class="form-label">Upload File</label>
          <input type="file" class="form-control" name="file" id="file" required>
        </div>

        <button type="submit" name="submit" class="submit-btn">Submit Assignment</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
