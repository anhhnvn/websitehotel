<?php
session_start();
include "../connect.php";

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $message = "<p class='error-message'>Please enter your email and password completely.</p>";
    } else {
        // Get information from the Users table, without password encryption
        $query = "SELECT UserID, FullName, PasswordHash, Role FROM Users WHERE Email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) === 1) {
                mysqli_stmt_bind_result($stmt, $userid, $fullname, $password_in_db, $role);
                mysqli_stmt_fetch($stmt);

                // Compare passwords directly
                if ($password === $password_in_db) {
                    $_SESSION['user_id'] = $userid;
                    $_SESSION['user_name'] = $fullname;
                    $_SESSION['role'] = $role;

                    // Redirect
                    if (strtolower($role) === 'admin') {
                        header("Location: ../dashboard_admin.php");
                        exit();
                    } else {
                        header("Location: ../main.php");
                        exit();
                    }
                } else {
                    $message = "<p class='error-message'>Incorrect password.</p>";
                }
            } else {
                $message = "<p class='error-message'>Account not found with this email.</p>";
            }
        } else {
            $message = "<p class='error-message'>Query error.</p>";
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background: linear-gradient(135deg, #fffbe6 0%, #ffe0b2 100%);
            font-family: 'Montserrat', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form {
            background-color: #fff;
            padding: 32px 40px;
            border-radius: 16px;
            box-shadow: 0 6px 24px rgba(255, 193, 7, 0.15);
            width: 100%;
            max-width: 400px;
            border: 1px solid #ffb300;
        }

        .login-title {
            text-align: center;
            margin-bottom: 28px;
            color: #ff9800;
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 1px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ff9800;
            font-size: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #ffd54f;
            border-radius: 8px;
            font-size: 15px;
            background: #fffde7;
            color: #333;
            transition: border-color 0.2s;
        }

        input:focus {
            border-color: #ffb300;
            outline: none;
        }

        button {
            width: 100%;
            padding: 13px;
            background: linear-gradient(90deg, #ff9800 0%, #ffb300 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.12);
            margin-top: 8px;
            transition: background 0.2s;
        }

        button:hover {
            background: linear-gradient(90deg, #ffb300 0%, #ff9800 100%);
        }

        .note {
            text-align: center;
            margin-top: 16px;
            font-size: 15px;
            color: #ff9800;
        }
        .note a {
            color: #ff9800;
            text-decoration: underline;
            font-weight: 600;
            margin: 0 4px;
        }
        .note a:hover {
            color: #ffb300;
        }
        .error-message {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <form class="login-form" action="login.php" method="post">
        <h2 class="login-title">Login</h2>
        
        <?php 
        if (!empty($message)) {
            echo $message; 
        }
        ?>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>

        <div class="note">
            Don't have an account? <a href="register.php">Register</a>/<a href="../main.php">Home</a>
        </div>
    </form>
</body>
</html>