<?php
session_start();
include "connect.php"; // Đường dẫn đúng tới connect.php

// Xử lý Thêm hoặc Cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_user'])) {
    $id = $_POST['user_id'] ?? '';
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($id) {
        // Cập nhật (không thay mật khẩu)
        $sql = "UPDATE Users SET FullName=?, Email=?, PhoneNumber=?, Address=? WHERE UserID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $fullname, $email, $phone, $address, $id);
    } else {
        // Thêm mới (có mật khẩu)
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO Users (FullName, Email, PasswordHash, PhoneNumber, Address) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $fullname, $email, $password, $phone, $address);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: user.php");
        exit();
    } else {
        echo "Lỗi: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM Users WHERE UserID=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: user.php");
    exit();
}

// Lấy danh sách người dùng
$result = mysqli_query($conn, "SELECT UserID, FullName, Email, PhoneNumber, Address FROM Users");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Người Dùng</title>
    <style>
        /* Bố cục toàn trang */
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #fffbe6 0%, #ffe0b2 100%);
            font-family: Arial, sans-serif;
        }

        /* Container chính */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background-color: #fff;
            border: 1px solid #000;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Tiêu đề */
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form */
        form input,
        form select {
            display: block;
            width: calc(100% - 40px);
            margin: 10px 20px;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form button[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            margin: 10px 20px;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        form button[type="submit"]:hover {
            background-color: #8cd0ec;
        }

        /* Bảng dữ liệu */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #599de6;
            color: white;
        }

        td {
            background-color: #fdf5fa;
            word-break: break-word;
        }

        /* Nút hành động */
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            margin: 2px;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            background: linear-gradient(90deg, #ff9800 0%, #ffb300 100%);
            transition: background 0.3s;
        }
        .btn:hover, .btn:focus {
            background: linear-gradient(90deg, #fb8c00 0%, #ffa000 100%);
            color: #fff;
        }

        .edit-btn {
            background: linear-gradient(90deg, #ff9800 0%, #ffb300 100%);
        }

        .delete-btn {
            background: linear-gradient(90deg, #e53935 0%, #ff7043 100%);
        }

        .back-btn {
            display: inline-block;
            margin-top: 10px;
            background: linear-gradient(90deg, #ff9800 0%, #ffb300 100%);
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: linear-gradient(90deg, #fb8c00 0%, #ffa000 100%);
        }

        /* Responsive cho thiết bị nhỏ */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            form input,
            form select {
                width: calc(100% - 20px);
                margin: 10px auto;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                text-align: left;
                background-color: #de98d1;
                color: white;
            }

            td {
                margin-bottom: 10px;
                padding: 10px;
            }

            tr {
                margin-bottom: 15px;
                border-bottom: 1px solid #ccc;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Quản lý Người Dùng</h2>

        <form method="POST">
            <input type="hidden" name="user_id" id="user_id">
            <input type="text" name="fullname" id="fullname" placeholder="Họ tên" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="text" name="phone" id="phone" placeholder="Số điện thoại">
            <input type="text" name="address" id="address" placeholder="Địa chỉ">
            <input type="password" name="password" id="password" placeholder="Mật khẩu">
            <button type="submit" name="save_user" class="btn">Save</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td data-label="ID"><?= $row['UserID'] ?></td>
                        <td data-label="Họ tên"><?= htmlspecialchars($row['FullName']) ?></td>
                        <td data-label="Email"><?= htmlspecialchars($row['Email']) ?></td>
                        <td data-label="Số điện thoại"><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                        <td data-label="Địa chỉ"><?= htmlspecialchars($row['Address']) ?></td>
                        <td data-label="Hành động">
                            <button class="btn edit-btn" onclick="editUser(
                                '<?= $row['UserID'] ?>',
                                '<?= htmlspecialchars($row['FullName']) ?>',
                                '<?= htmlspecialchars($row['Email']) ?>',
                                '<?= htmlspecialchars($row['PhoneNumber']) ?>',
                                '<?= htmlspecialchars($row['Address']) ?>'
                            )">Sửa</button>
                            <a href="?delete=<?= $row['UserID'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn back-btn">Quay lại</a>
        <a href="admin_rooms.php" class="btn" style="background:#007bff;color:#fff;margin-left:10px;">Room Management (Admin)</a>
    </div>

    <script>
        function editUser(id, fullname, email, phone, address) {
            document.getElementById('user_id').value = id;
            document.getElementById('fullname').value = fullname;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('address').value = address;
            document.getElementById('password').removeAttribute('required'); // Không yêu cầu nhập mật khẩu khi sửa
            document.getElementById('password').style.display = 'none'; // Ẩn trường mật khẩu
        }
    </script>
</body>
</html>
