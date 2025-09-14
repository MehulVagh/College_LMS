<?php
session_start();
include '../config/db.php';

// Only admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if(!$id){
    die("User ID missing!");
}

// Fetch existing user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    die("User not found!");
}

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if(!empty($password)){
        // ✅ Update with new password (plain text for now, you can switch to password_hash later)
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, password=? WHERE id=?");
        $stmt->execute([$name, $email, $role, $password, $id]);
    } else {
        // ✅ Update without changing password
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
        $stmt->execute([$name, $email, $role, $id]);
    }

    header("Location: users.php?msg=User updated");
    exit;
}
?>

<h2>Edit User</h2>
<form method="POST">
    Name: <input type="text" name="name" value="<?= $user['name']; ?>" required><br>
    Email: <input type="email" name="email" value="<?= $user['email']; ?>" required><br>
    Role: 
    <select name="role">
        <option value="admin" <?= $user['role']=="admin"?"selected":""; ?>>Admin</option>
        <option value="faculty" <?= $user['role']=="faculty"?"selected":""; ?>>Faculty</option>
        <option value="student" <?= $user['role']=="student"?"selected":""; ?>>Student</option>
    </select><br>
    Password: <input type="password" name="password" placeholder="Leave blank to keep same"><br>
    <button type="submit" name="update">Update User</button>
</form>
