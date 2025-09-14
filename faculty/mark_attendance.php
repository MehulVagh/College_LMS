<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['faculty_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$course_id = $_GET['course_id'] ?? null;
if(!$course_id) die("Course not selected");

// Fetch students enrolled in this course
$stmt = $conn->prepare("SELECT students.id, students.name FROM students
    INNER JOIN enrollments ON students.id = enrollments.student_id
    WHERE enrollments.course_id = ?");
$stmt->execute([$course_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['submit'])){
    $date = $_POST['date'];
    foreach($_POST['status'] as $student_id => $status){
        $stmt = $conn->prepare("INSERT INTO attendance (course_id, student_id, date, status) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status=?");
        $stmt->execute([$course_id, $student_id, $date, $status, $status]);
    }
    $success = "Attendance marked successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
</head>
<body>
<h2>Mark Attendance</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    <label>Date:</label>
    <input type="date" name="date" required>
    <table border="1">
        <tr>
            <th>Student</th>
            <th>Status</th>
        </tr>
        <?php foreach($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['name']); ?></td>
            <td>
                <select name="status[<?= $student['id']; ?>]">
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit" name="submit">Submit Attendance</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
