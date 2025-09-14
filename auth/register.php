<?php
include '../config/db.php';

if(isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password
    $role = $_POST['role']; // admin / faculty / student

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->rowCount() > 0){
        echo "Email already registered!";
    } else {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users(name, email, password, role) VALUES(?,?,?,?)");
        if($stmt->execute([$name, $email, $password, $role])){
            echo "Registration successful!";
        } else {
            echo "Error in registration!";
        }
    }
}
?>
<form method="POST">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    Role:
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="faculty">Faculty</option>
        <option value="student">Student</option>
    </select><br>
    <button type="submit" name="register">Register</button>
</form>
