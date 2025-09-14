<?php
session_start();
include '../config/db.php';

if(!isset($_SESSION['faculty_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$faculty_id = $_SESSION['faculty_id'];
$course_id = $_GET['course_id'] ?? null;
if(!$course_id) die("Course not selected");

if(isset($_POST['submit'])){
    $file = $_FILES['material']['name'];
    $tmp = $_FILES['material']['tmp_name'];

    if($file){
        $upload_dir = '../uploads/materials/';
        move_uploaded_file($tmp, $upload_dir.$file);

        $stmt = $conn->prepare("INSERT INTO materials (course_id, faculty_id, file_name, uploaded_on) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$course_id, $faculty_id, $file]);

        $success = "Material uploaded successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Material</title>
</head>
<body>
<h2>Upload Material</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="material" required>
    <button type="submit" name="submit">Upload</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
