<?php
session_start();
include '../config/db.php';

// Ensure only faculty can access
if ($_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit();
}

$faculty_id = $_SESSION['id'];

// Fetch courses assigned to this faculty
$courses = $conn->query("SELECT * FROM courses WHERE faculty_id = $faculty_id");

if (isset($_POST['mark'])) {
    $course_id = $_POST['course_id'];
    $date = $_POST['date'];

    foreach ($_POST['status'] as $student_id => $status) {
        $stmt = $conn->prepare("INSERT INTO attendance (course_id, faculty_id, student_id, attendance_date, status) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $faculty_id, $student_id, $date, $status]);
    }

    echo "<p style='color:green'>Attendance saved successfully!</p>";
}
?>

<h2>Mark Attendance</h2>
<form method="post">
    <label>Select Course:</label>
    <select name="course_id" required>
        <?php while ($c = $courses->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?= $c['course_id'] ?>"><?= $c['course_name'] ?></option>
        <?php } ?>
    </select><br><br>

    <label>Date:</label>
    <input type="date" name="date" required><br><br>

    <h3>Students:</h3>
    <?php
    if (isset($_GET['course_id'])) {
        $cid = $_GET['course_id'];
        $students = $conn->query("SELECT u.id, u.name 
                                  FROM student_courses sc 
                                  JOIN users u ON sc.student_id = u.id 
                                  WHERE sc.course_id = $cid");

        while ($s = $students->fetch(PDO::FETCH_ASSOC)) {
            echo $s['name'] . " 
                <select name='status[{$s['id']}]'>
                    <option value='Present'>Present</option>
                    <option value='Absent'>Absent</option>
                    <option value='Late'>Late</option>
                    <option value='Excused'>Excused</option>
                </select><br>";
        }
    } else {
        echo "<p>Select a course above to view students.</p>";
    }
    ?>
    <br>
    <button type="submit" name="mark">Save Attendance</button>
</form>
