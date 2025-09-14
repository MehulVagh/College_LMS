<?php
include '../config/db.php';
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);
header("Location: manage_users.php");
?>
