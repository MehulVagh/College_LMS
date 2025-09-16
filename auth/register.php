<?php
include '../config/db.php';

if(isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password
    $role = 'student'; // only student

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->rowCount() > 0){
        echo "<p style='color:red;'>Email already registered!</p>";
    } else {
        // Insert user with status 'pending' for faculty approval
        $status = 'pending';
        $stmt = $conn->prepare("INSERT INTO users(name, email, password, role, status) VALUES(?,?,?,?,?)");
        if($stmt->execute([$name, $email, $password, $role, $status])){
            echo "<p style='color:orange;'>Registration submitted! Awaiting faculty approval.</p>";
        } else {
            echo "<p style='color:red;'>Error in registration!</p>";
        }
    }
}
?>

<form method="POST">
    <label>Name:</label> 
    <input type="text" name="name" required><br><br>

    <label>Email:</label> 
    <input type="email" name="email" required><br><br>

    <label>Password:</label> 
    <input type="password" name="password" required><br><br>

    <button type="submit" name="register">Register as Student</button>
</form>
