<?php
// Include the database connection file
include "connect.php";

// Check if the request method is POST, which means the user has submitted the form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from the form
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $quantity = $_POST['quantity'];

    // SQL query to insert a new booking into the 'bookings' table
    $sql = "INSERT INTO bookings (fullname, email, phone, room_id, check_in, check_out, quantity) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // If the prepared statement is successful
    if ($stmt) {
        // Bind parameters to the statement to prevent SQL injection
        // 's' for string, 'i' for integer
        $stmt->bind_param("sssissi", $fullname, $email, $phone, $room_id, $check_in, $check_out, $quantity);
        // Execute the statement
        if ($stmt->execute()) {
            // If the booking is successful, show an alert and redirect to the rooms page
            echo "<script>alert('Booking successful!'); window.location.href='rooms.php';</script>";
        } else {
            // If there's an error during booking, display the error message
            echo "Error booking: " . $stmt->error;
        }
    } else {
        // If there's an error in the SQL query itself, display the error
        echo "SQL query error: " . $conn->error;
    }
} 
// If the request method is not POST, and there's a 'room_id' parameter in the URL
else if (isset($_GET['room_id'])) {
    // Get the room ID from the URL
    $room_id = $_GET['room_id'];

    // Get room details from the 'rooms' table
    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Room</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Book a Room: <?php echo htmlspecialchars($room['room_type']); ?></h2>
    <form method="POST" action="process_booking.php">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Check-in Date:</label>
            <input type="date" name="check_in" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Check-out Date:</label>
            <input type="date" name="check_out" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Number of Rooms:</label>
            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
        </div>
        <button type="submit" class="btn btn-success">Confirm Booking</button>
        <a href="rooms.php" class="btn btn-secondary">Go Back</a>
    </form>
</div>
</body>
</html>
<?php
} else {
    // If there's no room ID or the request is not POST, redirect back to the rooms list
    header("Location: rooms.php");
    exit();
}
?>
