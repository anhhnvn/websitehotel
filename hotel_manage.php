<?php
session_start();
include "connect.php";

// Check if user is logged in and is Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Add room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_room"])) {
    $room_number = $_POST["room_number"];
    $room_type = $_POST["room_type"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $description = $_POST["description"];
    $hotel_id = $_POST["hotel_id"];

    $image_name = null;
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $sql = "INSERT INTO rooms (room_number, room_type, price, quantity, description, image, hotel_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdissi", $room_number, $room_type, $price, $quantity, $description, $image_name, $hotel_id);
    $stmt->execute();
}

// Delete room
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM rooms WHERE id = $id");
    header("Location: hotel_manage.php");
    exit;
}

// Edit room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_room"])) {
    $id = $_POST["room_id"];
    $room_number = $_POST["room_number"];
    $room_type = $_POST["room_type"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $description = $_POST["description"];
    $hotel_id = $_POST["hotel_id"];

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        $sql = "UPDATE rooms SET room_number=?, room_type=?, price=?, quantity=?, description=?, image=?, hotel_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdissii", $room_number, $room_type, $price, $quantity, $description, $image_name, $hotel_id, $id);
    } else {
        $sql = "UPDATE rooms SET room_number=?, room_type=?, price=?, quantity=?, description=?, hotel_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisii", $room_number, $room_type, $price, $quantity, $description, $hotel_id, $id);
    }

    $stmt->execute();
    header("Location: hotel_manage.php");
    exit;
}

// Get rooms
$result = $conn->query("SELECT * FROM rooms");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hotel Room Management</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        h2 { color: #333; }
        form, table {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        input[type="text"], input[type="number"], textarea {
            padding: 8px;
            margin: 5px 0 10px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            cursor: pointer;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        img { width: 80px; height: auto; }
        .action-btn {
            padding: 6px 10px;
            margin: 2px;
            text-decoration: none;
            background-color: #2196F3;
            color: white;
            border-radius: 4px;
        }
        .delete-btn { background-color: #f44336; }
    </style>
</head>
<body>

<h2>Add New Room</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="room_number" placeholder="Room Number" required>
    <input type="text" name="room_type" placeholder="Room Type" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="number" name="quantity" placeholder="Quantity" required>
    <textarea name="description" placeholder="Room Description"></textarea>
    <input type="file" name="image">
    <input type="number" name="hotel_id" placeholder="Hotel ID" required>
    <input type="submit" name="add_room" value="Add Room">
</form>

<h2>Room List</h2>
<table>
    <tr>
        <th>Image</th>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Description</th>
        <th>Hotel ID</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php if ($row["image"]) echo "<img src='uploads/{$row["image"]}' alt='room'>"; ?></td>
            <td><?= htmlspecialchars($row["room_number"]) ?></td>
            <td><?= htmlspecialchars($row["room_type"]) ?></td>
            <td><?= number_format($row["price"], 0, ',', '.') ?> VND</td>
            <td><?= $row["quantity"] ?></td>
            <td><?= htmlspecialchars($row["description"]) ?></td>
            <td><?= $row["hotel_id"] ?></td>
            <td>
                <a class="action-btn" href="edit_room.php?id=<?= $row["id"] ?>">Edit</a>
                <a class="action-btn delete-btn" href="?delete=<?= $row["id"] ?>" onclick="return confirm('Delete this room?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
