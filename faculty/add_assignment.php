<?php
session_start();
include '../config/db.php';

// Only allow faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

// Get faculty ID
$faculty_id = $_SESSION['user_id'];

// Fetch courses assigned to this faculty
$stmt = $conn->prepare("SELECT * FROM courses WHERE faculty_id = ?");
$stmt->execute([$faculty_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no courses assigned, fetch all courses as fallback
if(empty($courses)){
    $stmt = $conn->prepare("SELECT * FROM courses");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if(isset($_POST['submit'])){
    $course_id = $_POST['course_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

    if(!$course_id){
        echo "❌ Please select a course!";
    } else {
        // Optional file upload
        $file = null;
        if(!empty($_FILES['file']['name'])){
            $file = time() . "_" . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/" . $file);
        }

        // Insert assignment into database
        $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, due_date, file) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $title, $description, $due_date, $file]);

        echo "✅ Assignment added successfully!";
    }
}
?>

<h2>Add Assignment</h2>

<?php if(empty($courses)): ?>
    <p style="color:red;">No courses available. Please add a course first.</p>
<?php else: ?>
<form method="POST" enctype="multipart/form-data">
    <label>Course:</label>
    <select name="course_id" required>
        <option value="">--Select Course--</option>
        <?php foreach($courses as $c): ?>
            <option value="<?= $c['course_id']; ?>"><?= htmlspecialchars($c['course_name']); ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Title:</label>
    <input type="text" name="title" required><br><br>

    <label>Description:</label>
    <textarea name="description"></textarea><br><br>

    <label>Due Date:</label>
    <input type="date" name="due_date" required><br><br>

    <label>File:</label>
    <input type="file" name="file"><br><br>

    <button type="submit" name="submit">Add Assignment</button>
</form>
<?php endif; ?>
