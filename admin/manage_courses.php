<?php
include '../config/db.php';

// Fetch courses with faculty name
$stmt = $conn->prepare("
    SELECT courses.*, users.name as faculty_name 
    FROM courses 
    LEFT JOIN users ON courses.faculty_id = users.id
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Courses</h2>
<a href="add_course.php">Add Course</a>
<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Course Name</th>
    <th>Course Code</th>
    <th>Faculty</th>
    <th>Actions</th>
</tr>

<?php foreach($courses as $course): ?>
<tr>
    <td><?= $course['course_id'] ?></td>
    <td><?= htmlspecialchars($course['course_name']) ?></td>
    <td><?= htmlspecialchars($course['course_code']) ?></td>
    <td><?= htmlspecialchars($course['faculty_name'] ?? '-') ?></td>
    <td>
        <a href="edit_course.php?id=<?= $course['course_id'] ?>">Edit</a> | 
        <a href="delete_course.php?id=<?= $course['course_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
