<?php
session_start();
// Only allow admin access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Welcome Admin!</h2>
        <div class="mb-3">
            <a href="main.php" class="btn btn-primary">Homepage</a>
            <a href="user.php" class="btn btn-success">User Management</a>
            <a href="admin_rooms.php" class="btn btn-info">Room Management</a>
        </div>
        <p>Select the function you want to manage.</p>
    </div>
</body>
</html>