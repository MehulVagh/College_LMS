<?php
session_start();
include '../config/db.php';

// Only allow students
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch all assignments
$stmt = $conn->query("SELECT a.id, a.title, c.course_name FROM assignments a JOIN courses c ON a.course_id = c.course_id");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['submit'])){
    $assignment_id = $_POST['assignment_id'];

    if(!empty($_FILES['file']['name'])){
        $file = time() . "_" . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/" . $file);

        // Insert into submissions table
        $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, file) VALUES (?, ?, ?)");
        $stmt->execute([$assignment_id, $student_id, $file]);

        echo "✅ Assignment submitted successfully!";
    } else {
        echo "⚠️ Please choose a file to submit!";
    }
}
?>

<h2>Submit Assignment</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Select Assignment:</label>
    <select name="assignment_id" required>
        <?php foreach($assignments as $a): ?>
            <option value="<?= $a['id']; ?>">
                <?= $a['course_name']; ?> - <?= $a['title']; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Upload File:</label>
    <input type="file" name="file" required><br><br>

    <button type="submit" name="submit">Submit Assignment</button>
</form>
