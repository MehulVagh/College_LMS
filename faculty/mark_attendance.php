<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

$faculty_id = $_SESSION['user_id'];

// Step 1: If course not chosen yet
if(!isset($_GET['course_id'])){
    $stmt = $conn->prepare("SELECT course_id, course_name FROM courses WHERE faculty_id = ?");
    $stmt->execute([$faculty_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Select Course - Mark Attendance</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                font-family: "Segoe UI", sans-serif;
                min-height: 100vh;
                margin: 0;
                background: linear-gradient(135deg,#1e1b4b,#6d28d9,#0f172a);
                color: #fff;
            }
            .container { max-width: 600px; margin: 50px auto; }
            .page-title { text-align: center; font-size: 1.8rem; margin-bottom: 2rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.5); }
            .btn-primary, .btn-secondary { width: 100%; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2 class="page-title">Select Course for Attendance</h2>
            <form method="get">
                <div class="mb-3">
                    <label for="course_id" class="form-label">Choose Course:</label>
                    <select name="course_id" id="course_id" class="form-select" required>
                        <option value="">-- Select --</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['course_id']; ?>"><?= htmlspecialchars($course['course_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Continue</button>
            </form>
            <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Step 2: Course selected
$course_id = $_GET['course_id'];
$date = $_POST['date'] ?? date('Y-m-d');

// Fetch students
$stmt = $conn->prepare("
    SELECT u.id AS student_id, u.name
    FROM users u
    INNER JOIN student_courses sc ON u.id = sc.student_id
    WHERE sc.course_id = ? AND u.role='student'
");
$stmt->execute([$course_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if attendance already exists for this course and date
$stmt = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE course_id = ? AND attendance_date = ?");
$stmt->execute([$course_id, $date]);
$attendance_exists = $stmt->fetchColumn() > 0;

if(isset($_POST['submit'])){
    if($attendance_exists){
        $error = "❌ Attendance for this course and date is already saved!";
    } else {
        foreach($students as $student){
            $sid = $student['student_id'];
            $status = $_POST['status'][$sid] ?? 'Absent';
            $stmt = $conn->prepare("
                INSERT INTO attendance (course_id, faculty_id, student_id, attendance_date, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$course_id, $faculty_id, $sid, $date, $status]);
        }
        $success = "✅ Attendance saved successfully!";
        $attendance_exists = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Attendance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    min-height: 100vh;
    margin: 0;
    background: linear-gradient(135deg,#1e1b4b,#6d28d9,#0f172a);
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
.logout-btn {
    background: linear-gradient(90deg, #ef4444, #dc2626);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}
.logout-btn:hover { opacity: 0.9; }

.container { max-width:900px; margin:30px auto; }
.page-title { text-align:center; font-size:1.8rem; margin-bottom:2rem; font-weight:600; text-shadow:1px 1px 3px rgba(0,0,0,0.5); }

.table-wrapper { background: rgba(255,255,255,0.05); padding:1rem; border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.3); overflow-x:auto; }
table { width:100%; border-collapse: separate; border-spacing: 0; color:#fff; min-width:600px; }
thead { background: linear-gradient(90deg, #3b82f6, #2563eb); color:#fff; }
th, td { padding:12px 16px; text-align:left; border: 1px solid #00ffff; }
td { color:#e0f7fa; font-weight:500; }
tbody tr { background: rgba(255,255,255,0.08); transition: 0.3s; }
tbody tr:hover { background: rgba(0,255,255,0.15); }

.btn-status { width:100%; }
.btn-status.active { opacity: 0.8; }
.btn-Present { background: #10b981; color:#fff; }
.btn-Absent { background: #ef4444; color:#fff; }

.alert { margin-top:1rem; }

@media (max-width:768px){ th, td{ padding:10px 12px; font-size:0.9rem; } }
@media (max-width:480px){ th, td{ padding:8px 10px; font-size:0.8rem; } .page-title{ font-size:1.4rem; } }
</style>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body>
<header>
    <h1>📝 Mark Attendance</h1>
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</header>

<div class="container">
    <h2 class="page-title">Course: <?= htmlspecialchars($course_id) ?></h2>
    <?php 
        if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
        if(isset($error)) echo "<div class='alert alert-danger'>$error</div>";
    ?>

    <form method="post">
        <div class="mb-3">
            <label for="date" class="form-label">Date:</label>
            <input type="date" name="date" id="date" class="form-control" value="<?= $date ?>" required <?= $attendance_exists ? 'readonly' : '' ?>>
        </div>

        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Present</th>
                    <th>Absent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student):
                    $sid = $student['student_id'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($student['name']); ?></td>
                    <td><button type="button" class="btn btn-status btn-Present" data-student="<?= $sid ?>" <?= $attendance_exists ? 'disabled' : '' ?>>Present</button></td>
                    <td><button type="button" class="btn btn-status btn-Absent" data-student="<?= $sid ?>" <?= $attendance_exists ? 'disabled' : '' ?>>Absent</button></td>
                    <input type="hidden" name="status[<?= $sid ?>]" id="status-<?= $sid ?>" value="">
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <button type="submit" name="submit" class="btn btn-success mt-3" <?= $attendance_exists ? 'disabled' : '' ?>>Save Attendance</button>
    </form>

    <a href="mark_attendance.php" class="btn btn-secondary mt-3">⬅ Select Another Course</a>
    <a href="dashboard.php" class="btn btn-primary mt-3">🏠 Back to Dashboard</a>
</div>

<script>
$(document).ready(function(){
    $('.btn-status').click(function(){
        var student_id = $(this).data('student');
        var status = $(this).hasClass('btn-Present') ? 'Present' : 'Absent';
        $('#status-'+student_id).val(status);

        $('button[data-student="'+student_id+'"]').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
</body>
</html>
