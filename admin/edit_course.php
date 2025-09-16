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

// Check if course_id is set
if(!isset($_GET['id'])){
    die("Course ID is required.");
}
$course_id = $_GET['id'];

// Fetch course details
$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$course){
    die("Course not found.");
}

// Fetch all faculty for dropdown
$stmt2 = $conn->prepare("SELECT id, name FROM users WHERE role = 'faculty'");
$stmt2->execute();
$faculties = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Update course on form submission
if(isset($_POST['submit'])){
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $faculty_id = $_POST['faculty_id'] ?: null; // allow null if not assigned

    $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_code = ?, faculty_id = ? WHERE course_id = ?");
    if($stmt->execute([$course_name, $course_code, $faculty_id, $course_id])){
        header("Location: manage_courses.php");
        exit;
    } else {
        $error = "Error updating course.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Course - Admin</title>
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
    .error {
      text-align: center;
      margin-bottom: 1rem;
      font-weight: bold;
      color: #f87171;
    }
  </style>
</head>
<body>
  <header>
    <h1>✏️ Edit Course</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
  </header>

  <div class="container">
    <h2 class="page-title">Update Course Details</h2>

    <?php if(isset($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="form-box">
      <form method="POST" action="">
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" id="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required>

        <label for="course_code">Course Code</label>
        <input type="text" name="course_code" id="course_code" value="<?= htmlspecialchars($course['course_code']) ?>" required>

        <label for="faculty_id">Assign Faculty</label>
        <select name="faculty_id" id="faculty_id">
          <option value="">--None--</option>
          <?php foreach($faculties as $f): ?>
            <option value="<?= $f['id'] ?>" <?= $course['faculty_id'] == $f['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($f['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" name="submit" class="btn submit-btn">✅ Update Course</button>
        <a href="manage_courses.php" class="btn back-btn">⬅ Back</a>
      </form>
    </div>
  </div>
</body>
</html>
