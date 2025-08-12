<?php
session_start();
// Check admin privileges
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
    // Get basic form data
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

    // Handle image upload
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
                $message = "<div class='alert alert-danger'>Error uploading image.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Only JPG or JPEG files are accepted.</div>";
        }
    } else {
        // If no new file is uploaded, keep the old image
        $imageUrl = $_POST['current_image_url'] ?? '';
    }

    // If there are no errors, handle add/update/delete
    if (empty($message)) {
        if (isset($_POST['add_room'])) {
            // Add new room
            if ($roomName && $price) {
                $insert = "INSERT INTO rooms (room_name, category_id, description, short_description, image_url, address, price, capacity, size, has_wifi, has_bathtub, has_balcony, available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sissssdiidiii", $roomName, $categoryId, $description, $shortDescription, $imageUrl, $address, $price, $capacity, $size, $hasWifi, $hasBathtub, $hasBalcony, $available);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Room added successfully!</div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Error adding room: " . mysqli_error($conn) . "</div>";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $message = "<div class='alert alert-danger'>Please enter all required room information!</div>";
            }
        } elseif (isset($_POST['update_room']) && $roomId) {
            // Update room
            if ($roomName && $price) {
                $update = "UPDATE rooms SET room_name=?, category_id=?, description=?, short_description=?, image_url=?, address=?, price=?, capacity=?, size=?, has_wifi=?, has_bathtub=?, has_balcony=?, available=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $update);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sissssdiidiiii", $roomName, $categoryId, $description, $shortDescription, $imageUrl, $address, $price, $capacity, $size, $hasWifi, $hasBathtub, $hasBalcony, $available, $roomId);
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "<div class='alert alert-success'>Room updated successfully!</div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Error updating room: " . mysqli_error($conn) . "</div>";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $message = "<div class='alert alert-danger'>Please enter all required room information!</div>";
            }
        } elseif (isset($_POST['delete_room']) && $roomId) {
            // Delete room
            $delete = "DELETE FROM rooms WHERE id=?";
            $stmt = mysqli_prepare($conn, $delete);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $roomId);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "<div class='alert alert-success'>Room deleted successfully!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Error deleting room: " . mysqli_error($conn) . "</div>";
                }
                mysqli_stmt_close($stmt);
            }
        } elseif (isset($_POST['edit_room']) && $roomId) {
            // Pre-fill form for editing
            $room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE id = $roomId"));
            if ($room) {
                $roomName = $room['room_name'];
                $categoryId = $room['category_id'];
                $description = $room['description'];
                $shortDescription = $room['short_description'];
                $imageUrl = $room['image_url'];
                $address = $room['address'];
                $price = $room['price'];
                $capacity = $room['capacity'];
                $size = $room['size'];
                $hasWifi = $room['has_wifi'];
                $hasBathtub = $room['has_bathtub'];
                $hasBalcony = $room['has_balcony'];
                $available = $room['available'];
            }
        }
    }
}

// Get list of rooms
$rooms = [];
$result = mysqli_query($conn, "SELECT * FROM rooms");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Room Management - Admin</title>
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
        <a href="register/user/user.php" class="btn btn-orange me-2">Back to user management</a>
        <a href="main.php" class="btn btn-orange">Back to home page</a>
    </div>
    <h2 class="mb-4">Hotel Room Management</h2>
    <?php if ($message) echo $message; ?>
    <form method="post" enctype="multipart/form-data" class="card p-4 mb-4">
        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($roomId); ?>">
        <input type="hidden" name="current_image_url" value="<?php echo htmlspecialchars($imageUrl); ?>">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Room name</label>
                <input type="text" name="room_name" class="form-control" placeholder="Room name" value="<?php echo htmlspecialchars($roomName); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category ID</label>
                <input type="number" name="category_id" class="form-control" placeholder="Category ID" value="<?php echo htmlspecialchars($categoryId); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Short description</label>
                <input type="text" name="short_description" class="form-control" placeholder="Short description" value="<?php echo htmlspecialchars($shortDescription); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Room image (JPG)</label>
                <input type="file" name="image_file" accept=".jpg,.jpeg" class="form-control">
                <?php if ($imageUrl): ?>
                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Room image" class="room-image mt-2" />
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" placeholder="Room address" value="<?php echo htmlspecialchars($address); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Price</label>
                <input type="number" name="price" class="form-control" placeholder="Price" value="<?php echo htmlspecialchars($price); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" class="form-control" placeholder="Capacity" value="<?php echo htmlspecialchars($capacity); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Area (m2)</label>
                <input type="number" name="size" class="form-control" placeholder="Area (m2)" value="<?php echo htmlspecialchars($size); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Detailed description</label>
                <input type="text" name="description" class="form-control" placeholder="Detailed description" value="<?php echo htmlspecialchars($description); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Wifi</label>
                <select name="has_wifi" class="form-select">
                    <option value="1" <?php if ($hasWifi == 1) echo 'selected'; ?>>Yes</option>
                    <option value="0" <?php if ($hasWifi == 0) echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Bathtub</label>
                <select name="has_bathtub" class="form-select">
                    <option value="1" <?php if ($hasBathtub == 1) echo 'selected'; ?>>Yes</option>
                    <option value="0" <?php if ($hasBathtub == 0) echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Balcony</label>
                <select name="has_balcony" class="form-select">
                    <option value="1" <?php if ($hasBalcony == 1) echo 'selected'; ?>>Yes</option>
                    <option value="0" <?php if ($hasBalcony == 0) echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Available</label>
                <select name="available" class="form-select">
                    <option value="1" <?php if ($available == 1) echo 'selected'; ?>>Yes</option>
                    <option value="0" <?php if ($available == 0) echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <?php if ($roomId): ?>
                    <button type="submit" name="update_room" class="btn btn-orange w-100">Update</button>
                <?php else: ?>
                    <button type="submit" name="add_room" class="btn btn-orange w-100">Add</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <h4>Room list</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Room name</th>
                <th>Category</th>
                <th>Short description</th>
                <th>Image</th>
                <th>Address</th>
                <th>Price</th>
                <th>Capacity</th>
                <th>Area</th>
                <th>Wifi</th>
                <th>Bathtub</th>
                <th>Balcony</th>
                <th>Available</th>
                <th>Created at</th>
                <th>Action</th>
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
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="Room image" style="max-width:100px; border-radius:5px;">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($room['address'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['size']); ?></td>
                <td><?php echo $room['has_wifi'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo $room['has_bathtub'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo $room['has_balcony'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo $room['available'] ? 'Yes' : 'No'; ?></td>
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
                        <button type="submit" name="edit_room" class="btn btn-warning btn-sm">Edit</button>
                    </form>
                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this room?');">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        <button type="submit" name="delete_room" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

