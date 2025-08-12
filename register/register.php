<?php
include("../connect.php");
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $phone = trim($_POST["phone"] ?? '');
    $address = trim($_POST["address"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirmPassword = $_POST["confirm_password"] ?? '';

    // Validate input data
    if (empty($fullName) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirmPassword)) {
        $message = "<div class='alert alert-danger'>Please fill in all the required information.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger'>Invalid email address.</div>";
    } elseif ($password !== $confirmPassword) {
        $message = "<div class='alert alert-danger'>The passwords do not match.</div>";
    } else {
        // Check if email already exists
        $check_query = "SELECT 1 FROM Users WHERE Email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = "<div class='alert alert-danger'>This email already exists.</div>";
        } else {
            // Do not hash the password for easier login
            $insert_query = "INSERT INTO Users (FullName, Email, PasswordHash, PhoneNumber, Address, Role) VALUES (?, ?, ?, ?, ?, 'Customer')";
            $stmt_insert = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt_insert, "sssss", $fullName, $email, $password, $phone, $address);

            if (mysqli_stmt_execute($stmt_insert)) {
                $message = "<div class='alert alert-success'>Registration successful! Redirecting to login page...</div>";
                header("refresh:2;url=login.php");
            } else {
                $message = "<div class='alert alert-danger'>Error during registration: " . mysqli_error($conn) . "</div>";
            }
            mysqli_stmt_close($stmt_insert);
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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #fffbe6 0%, #ffe0b2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header bg-warning text-white text-center">
                        <h4>Register for an Account</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)) echo $message; ?>

                        <form action="register.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="text" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Register</button>
                        </form>

                        <div class="text-center mt-3">
                            Already have an account? <a href="login.php">Login</a> / <a href="../main.php">Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>