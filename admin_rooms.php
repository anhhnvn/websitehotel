<?php
session_start();
// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../login.php');
    exit();
}

include("connect.php");

$roomId = '';
$roomName = '';
$categoryId = '';
$description = '';
$shortDescription = '';
$imageUrl = '';
$address = '';
$price = '';
$capacity = '';
$size = '';
$hasWifi = 1;
$hasBathtub = 0;
$hasBalcony = 0;
$available = 1;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu form cơ bản
    $roomId = $_POST['room_id'] ?? '';
    $roomName = trim($_POST['room_name'] ?? '');
    $categoryId = trim($_POST['category_id'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $hasWifi = isset($_POST['has_wifi']) ? intval($_POST['has_wifi']) : 1;
    $hasBathtub = isset($_POST['has_bathtub']) ? intval($_POST['has_bathtub']) : 0;
    $hasBalcony = isset($_POST['has_balcony']) ? intval($_POST['has_balcony']) : 0;
    $available = isset($_POST['available']) ? intval($_POST['available']) : 1;

    // Xử lý upload ảnh
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $tmpName = $_FILES['image_file']['tmp_name'];
        $fileName = basename($_FILES['image_file']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg'])) {
            $newFileName = uniqid() . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $imageUrl = $destination;
            } else {
                $message = "<div class='alert alert-danger'>Lỗi khi upload ảnh.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Chỉ chấp nhận file JPG hoặc JPEG.</div>";
        }
    } else {
        // Nếu không upload file mới, giữ lại ảnh cũ
        $imageUrl = $_POST['current_image_url'] ?? '';
    }

    // Nếu chưa có lỗi, xử lý thêm/cập nhật/xóa
    if (empty($message)) {
        if (isset($_POST['add_room'])) {
            // Thêm phòng mới
            if ($roomName && $price) {
                $insert = "INSERT INTO rooms (room_name, category_id, description, short_description, image_url, address, price, capacity, size, has_wifi, has_bathtub, has_balcony, available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sissssdiidiii", $roomName, $categoryId, $description, $shortDescription, $imageUrl, $address, $price, $capacity, $size, $hasWifi, $hasBathtub, $hasBalcony, $available);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Thêm phòng thành công!</div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Lỗi khi thêm phòng: " . mysqli_error($conn) . "</div>";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $message = "<div class='alert alert-danger'>Vui lòng nhập đầy đủ thông tin phòng!</div>";
            }
        } elseif (isset($_POST['update_room']) && $roomId) {
            // Cập nhật phòng
            if ($roomName && $price) {
                $update = "UPDATE rooms SET room_name=?, category_id=?, description=?, short_description=?, image_url=?, address=?, price=?, capacity=?, size=?, has_wifi=?, has_bathtub=?, has_balcony=?, available=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $update);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sissssdiidiiii", $roomName, $categoryId, $description, $shortDescription, $imageUrl, $address, $price, $capacity, $size, $hasWifi, $hasBathtub, $hasBalcony, $available, $roomId);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Cập nhật phòng thành công!</div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Lỗi khi cập nhật phòng: " . mysqli_error($conn) . "</div>";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $message = "<div class='alert alert-danger'>Vui lòng nhập đầy đủ thông tin phòng!</div>";
            }
        } elseif (isset($_POST['delete_room']) && $roomId) {
            // Xóa phòng
            $delete = "DELETE FROM rooms WHERE id=?";
            $stmt = mysqli_prepare($conn, $delete);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $roomId);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "<div class='alert alert-success'>Đã xóa phòng thành công!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Lỗi khi xóa phòng: " . mysqli_error($conn) . "</div>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Lấy danh sách phòng
$rooms = [];
$result = mysqli_query($conn, "SELECT * FROM rooms");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quản lý phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .btn-orange {
            background: linear-gradient(90deg, #ff9800 0%, #ffb300 100%);
            color: #fff;
            border: none;
            transition: background 0.3s;
        }
        .btn-orange:hover, .btn-orange:focus {
            background: linear-gradient(90deg, #fb8c00 0%, #ffa000 100%);
            color: #fff;
        }
        img.room-image {
            max-width: 150px;
            border-radius: 6px;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #fffbe6 0%, #ffe0b2 100%);">
<div class="container py-5">
    <div class="mb-4">
        <a href="register/user/user.php" class="btn btn-orange me-2">Quay lại quản lý người dùng</a>
        <a href="main.php" class="btn btn-orange">Quay về trang chủ</a>
    </div>
    <h2 class="mb-4">Quản lý phòng khách sạn</h2>
    <?php if ($message) echo $message; ?>
    <form method="post" enctype="multipart/form-data" class="card p-4 mb-4">
        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($roomId); ?>">
        <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($imageUrl); ?>">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tên phòng</label>
                <input type="text" name="room_name" class="form-control" placeholder="Tên phòng" value="<?php echo htmlspecialchars($roomName); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category ID</label>
                <input type="number" name="category_id" class="form-control" placeholder="Category ID" value="<?php echo htmlspecialchars($categoryId); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mô tả ngắn</label>
                <input type="text" name="short_description" class="form-control" placeholder="Mô tả ngắn" value="<?php echo htmlspecialchars($shortDescription); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Ảnh phòng (JPG)</label>
                <input type="file" name="image_file" accept=".jpg,.jpeg" class="form-control">
                <?php if ($imageUrl): ?>
                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Ảnh phòng" class="room-image mt-2" />
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="address" class="form-control" placeholder="Địa chỉ phòng" value="<?php echo htmlspecialchars($address); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Giá</label>
                <input type="number" name="price" class="form-control" placeholder="Giá" value="<?php echo htmlspecialchars($price); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sức chứa</label>
                <input type="number" name="capacity" class="form-control" placeholder="Sức chứa" value="<?php echo htmlspecialchars($capacity); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Diện tích (m2)</label>
                <input type="number" name="size" class="form-control" placeholder="Diện tích (m2)" value="<?php echo htmlspecialchars($size); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Mô tả chi tiết</label>
                <input type="text" name="description" class="form-control" placeholder="Mô tả chi tiết" value="<?php echo htmlspecialchars($description); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Wifi</label>
                <select name="has_wifi" class="form-select">
                    <option value="1" <?php if ($hasWifi == 1) echo 'selected'; ?>>Có</option>
                    <option value="0" <?php if ($hasWifi == 0) echo 'selected'; ?>>Không</option>
                </select>
            </div>
                        <div class="col-md-2">
                <label class="form-label">Bồn tắm</label>
                <select name="has_bathtub" class="form-select">
                    <option value="1" <?php if ($hasBathtub == 1) echo 'selected'; ?>>Có</option>
                    <option value="0" <?php if ($hasBathtub == 0) echo 'selected'; ?>>Không</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Ban công</label>
                <select name="has_balcony" class="form-select">
                    <option value="1" <?php if ($hasBalcony == 1) echo 'selected'; ?>>Có</option>
                    <option value="0" <?php if ($hasBalcony == 0) echo 'selected'; ?>>Không</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Còn phòng</label>
                <select name="available" class="form-select">
                    <option value="1" <?php if ($available == 1) echo 'selected'; ?>>Còn</option>
                    <option value="0" <?php if ($available == 0) echo 'selected'; ?>>Hết</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <?php if ($roomId): ?>
                    <button type="submit" name="update_room" class="btn btn-orange w-100">Cập nhật</button>
                <?php else: ?>
                    <button type="submit" name="add_room" class="btn btn-orange w-100">Thêm</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <h4>Danh sách phòng</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên phòng</th>
                <th>Category</th>
                <th>Mô tả ngắn</th>
                <th>Ảnh</th>
                <th>Địa chỉ</th>
                <th>Giá</th>
                <th>Sức chứa</th>
                <th>Diện tích</th>
                <th>Wifi</th>
                <th>Bồn tắm</th>
                <th>Ban công</th>
                <th>Còn phòng</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo $room['id']; ?></td>
                <td><?php echo htmlspecialchars($room['room_name']); ?></td>
                <td><?php echo htmlspecialchars($room['category_id']); ?></td>
                <td><?php echo htmlspecialchars($room['short_description']); ?></td>
                <td>
                    <?php if ($room['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="Ảnh phòng" style="max-width:100px; border-radius:5px;">
                    <?php else: ?>
                        Không có ảnh
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($room['address'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['size']); ?></td>
                <td><?php echo $room['has_wifi'] ? 'Có' : 'Không'; ?></td>
                <td><?php echo $room['has_bathtub'] ? 'Có' : 'Không'; ?></td>
                <td><?php echo $room['has_balcony'] ? 'Có' : 'Không'; ?></td>
                <td><?php echo $room['available'] ? 'Còn' : 'Hết'; ?></td>
                <td><?php echo $room['created_at']; ?></td>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room['room_name']); ?>">
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($room['category_id']); ?>">
                        <input type="hidden" name="short_description" value="<?php echo htmlspecialchars($room['short_description']); ?>">
                        <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($room['image_url']); ?>">
                        <input type="hidden" name="price" value="<?php echo htmlspecialchars($room['price']); ?>">
                        <input type="hidden" name="address" value="<?php echo htmlspecialchars($room['address'] ?? ''); ?>">
                        <input type="hidden" name="capacity" value="<?php echo htmlspecialchars($room['capacity']); ?>">
                        <input type="hidden" name="size" value="<?php echo htmlspecialchars($room['size']); ?>">
                        <input type="hidden" name="description" value="<?php echo htmlspecialchars($room['description']); ?>">
                        <input type="hidden" name="has_wifi" value="<?php echo $room['has_wifi']; ?>">
                        <input type="hidden" name="has_bathtub" value="<?php echo $room['has_bathtub']; ?>">
                        <input type="hidden" name="has_balcony" value="<?php echo $room['has_balcony']; ?>">
                        <input type="hidden" name="available" value="<?php echo $room['available']; ?>">
                        <button type="submit" name="edit_room" class="btn btn-warning btn-sm">Sửa</button>
                    </form>
                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng này?');">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        <button type="submit" name="delete_room" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

