<?php
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
        echo "Error updating course.";
    }
}

// Fetch all faculty for dropdown
$stmt2 = $conn->prepare("SELECT id, name FROM users WHERE role = 'faculty'");
$stmt2->execute();
$faculties = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Edit Course</h2>
<form method="POST">
    Course Name: <input type="text" name="course_name" value="<?= htmlspecialchars($course['course_name']); ?>" required><br>
    Course Code: <input type="text" name="course_code" value="<?= htmlspecialchars($course['course_code']); ?>" required><br>
    Assign Faculty:
    <select name="faculty_id">
        <option value="">--None--</option>
        <?php foreach($faculties as $faculty): ?>
            <option value="<?= $faculty['id'] ?>" <?= $course['faculty_id'] == $faculty['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($faculty['name']); ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit" name="submit">Update Course</button>
</form>
