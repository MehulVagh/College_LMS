<?php
session_start();
include '../config/db.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Find user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && $password == $user['password']){ 
        // store session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if($user['role'] == 'admin')
            {
            header("Location: ../admin/dashboard.php");
        } 
        elseif($user['role'] == 'faculty')
            {
            header("Location: ../faculty/dashboard.php");
        } 
        else 
            {
            header("Location: ../student/dashboard.php");
        }
        exit;
    } 

    else
         {
        echo "Invalid email or password!";
    }
}
?>

<form method="POST">
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="login">Login</button>
</form>
