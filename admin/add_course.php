<?php
include '../config/db.php';

// Handle form submission
if(isset($_POST['submit'])){
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $description = trim($_POST['description']);

    // Simple validation
    if(empty($course_name) || empty($course_code)){
        $error = "Course Name and Course Code are required!";
    } else {
        // Check for duplicate course code
        $stmt = $conn->prepare("SELECT * FROM courses WHERE course_code = ?");
        $stmt->execute([$course_code]);
        if($stmt->rowCount() > 0){
            $error = "Course Code already exists!";
        } else {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO courses (course_name, course_code, description) VALUES (?, ?, ?)");
            $stmt->execute([$course_name, $course_code, $description]);
            $success = "Course added successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Add Course</h2>

    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="post">
        <label>Course Name</label>
        <input type="text" name="course_name" required>

        <label>Course Code</label>
        <input type="text" name="course_code" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <button type="submit" name="submit">Add Course</button>
    </form>
</div>
</body>
</html>
