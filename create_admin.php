<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        // Lưu thẳng mật khẩu (KHÔNG băm)
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            echo "Admin account created successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="text" name="password" required><br><br>

    <button type="submit">Create Admin</button>
</form>
