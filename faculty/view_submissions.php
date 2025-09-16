<?php
session_start();
include '../config/db.php';

// Only allow faculty
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty'){
    header("Location: ../auth/login.php");
    exit;
}

$faculty_id = $_SESSION['user_id'];

// Fetch submissions for assignments belonging to this faculty
$sql = "SELECT s.id AS submission_id, s.file_path, s.submitted_at, s.grade, 
               a.title AS assignment_title, c.course_name, u.name AS student_name 
        FROM submissions s 
        JOIN assignments a ON s.assignment_id = a.id 
        JOIN courses c ON a.course_id = c.course_id 
        JOIN users u ON s.student_id = u.id 
        WHERE c.faculty_id = :faculty_id 
        ORDER BY s.submitted_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute(['faculty_id' => $faculty_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Submissions - Faculty</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #1e1b4b, #6d28d9, #0f172a);
    color: #fff;
    min-height: 100vh;
    margin: 0;
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
.header-actions { display: flex; gap: 0.75rem; }
.btn-custom {
    border: none; padding: 8px 16px;
    border-radius: 6px; color: #fff;
    text-decoration: none; font-weight: 500;
    transition: 0.3s;
}
.profile-btn { background: linear-gradient(90deg, #3b82f6, #2563eb); }
.profile-btn:hover { opacity: 0.9; }
.logout-btn { background: linear-gradient(90deg, #ef4444, #dc2626); }
.logout-btn:hover { opacity: 0.9; }

.container { padding: 2rem; max-width: 1200px; }
.page-title {
    text-align: center; margin-bottom: 2rem;
    font-size: 1.8rem; font-weight: 600;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}
.table-wrapper {
    background: rgba(255,255,255,0.05);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    color: #fff;
    min-width: 700px;
}
thead {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    color: #fff;
}
th, td {
    padding: 12px 16px;
    text-align: left;
    border: 1px solid #00ffff;
}
td { color: #e0f7fa; font-weight: 500; }
tbody tr { background: rgba(255,255,255,0.08); transition: 0.3s; }
tbody tr:hover { background: rgba(0, 255, 255, 0.15); }

.grade-input { width: 60px; transition: 0.3s; }
.grade-saved {
    background-color: #d4edda !important;
    color: #000 !important;
    font-weight: 600;
}
.grade-btn {
    background: linear-gradient(90deg, #10b981, #059669);
    border: none; color: #fff;
    padding: 6px 12px; border-radius: 6px;
    font-size: 0.85rem; font-weight: 500; transition: 0.3s;
}
.grade-btn:hover { opacity: 0.85; }

.no-data { text-align: center; font-weight: bold; color: #fbbf24; }

@media (max-width: 768px) {
    th, td { padding: 10px 12px; font-size: 0.9rem; }
}
@media (max-width: 480px) {
    th, td { padding: 8px 10px; font-size: 0.8rem; }
    .page-title { font-size: 1.4rem; }
}
</style>
</head>
<body>
<header>
    <h1>📂 Student Submissions</h1>
    <div class="header-actions">
        
        <a href="../auth/logout.php" class="btn-custom logout-btn">🚪 Logout</a>
    </div>
</header>

<div class="container">
    <h2 class="page-title">Student Submissions</h2>
    <div class="table-wrapper">
        <?php if(count($submissions) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Assignment</th>
                        <th>Student</th>
                        <th>Submitted At</th>
                        <th>File</th>
                        <th>Grade</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($submissions as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['course_name']) ?></td>
                            <td><?= htmlspecialchars($s['assignment_title']) ?></td>
                            <td><?= htmlspecialchars($s['student_name']) ?></td>
                            <td><?= htmlspecialchars($s['submitted_at']) ?></td>
                            <td>
                                <?php if($s['file_path']): ?>
                                    <a href="../uploads/<?= htmlspecialchars($s['file_path']) ?>" target="_blank">📥 Download</a>
                                <?php else: ?>
                                    No file
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="text" class="grade-input <?= !empty($s['grade']) ? 'grade-saved' : '' ?>" 
                                       data-id="<?= $s['submission_id'] ?>" value="<?= htmlspecialchars($s['grade'] ?? '') ?>">
                            </td>
                            <td>
                                <button class="grade-btn" data-id="<?= $s['submission_id'] ?>">Save</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No submissions yet.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.grade-btn').click(function(){
        var $row = $(this).closest('tr');
        var $input = $row.find('.grade-input');
        var submission_id = $input.data('id');
        var grade = $input.val();

        $.ajax({
            url: 'grade_submission_ajax.php',
            method: 'POST',
            data: { submission_id: submission_id, grade: grade },
            success: function(response){
                alert(response);
                if(grade.trim() !== ""){
                    $input.addClass('grade-saved');
                }
            },
            error: function(){
                alert('Error updating grade.');
            }
        });
    });

    // Remove highlight when user edits input again
    $('.grade-input').on('input', function(){
        $(this).removeClass('grade-saved');
    });
});
</script>
</body>
</html>
