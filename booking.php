<?php
session_start();
include_once "connect.php";
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = intval($_POST['guests'] ?? 1);
    $note = trim($_POST['note'] ?? '');
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    if ($payment_method) {
        $note = ($note ? $note . ' | ' : '') . 'Payment method: ' . $payment_method;
    }
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    // Get room price
    $price = 0;
    if ($room_id) {
        $res = $conn->query("SELECT price FROM rooms WHERE id=" . intval($room_id));
        if ($res && $row = $res->fetch_assoc()) {
            $price = floatval($row['price']);
        }
    }

    // Calculate number of nights
    $total_price = 0;
    if ($check_in && $check_out && $price) {
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $interval = $date1->diff($date2);
        $nights = $interval->days;
        if ($nights < 1) $nights = 1;
        $total_price = $nights * $price;
    }

    // Required validation
    if ($room_id && $check_in && $check_out) {
        $stmt = $conn->prepare("INSERT INTO booking (room_id, user_id, check_in, check_out, total_price, guests, note, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        if ($stmt === false) {
            $error = "SQL Error: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("iissdis", $room_id, $user_id, $check_in, $check_out, $total_price, $guests, $note);
            if ($stmt->execute()) {
                $success = "Booking successful! We will contact you for confirmation.";
            } else {
                $error = "An error occurred while booking. Please try again.";
            }
            $stmt->close();
        }
    } else {
        $error = "Please enter all required information.";
    }
}

// Get list of rooms to select
$rooms = [];
$res = $conn->query("SELECT id, room_name FROM rooms WHERE available=1 ORDER BY room_name");
if ($res) {
    while($r = $res->fetch_assoc()) $rooms[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a room - LuxStay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-12 max-w-xl">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Hotel Room Booking</h2>
        <?php if($success): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 text-center">
                <?php echo $success; ?><br>
                <strong>Payment Information:</strong><br>
                <?php echo htmlspecialchars($payment_method); ?>
            </div>
        <?php elseif($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" class="bg-white rounded-lg shadow-md p-8 space-y-5">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Select room type <span class="text-red-500">*</span></label>
                <select name="room_id" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <option value="">-- Select a room --</option>
                    <?php foreach($rooms as $room): ?>
                        <option value="<?php echo $room['id']; ?>" <?php if(isset($_POST['room_id']) && $_POST['room_id']==$room['id']) echo 'selected'; ?>><?php echo htmlspecialchars($room['room_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Check-in <span class="text-red-500">*</span></label>
                    <input type="date" name="check_in" required class="w-full px-4 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Check-out <span class="text-red-500">*</span></label>
                    <input type="date" name="check_out" required class="w-full px-4 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>">
                </div>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Number of guests</label>
                <input type="number" name="guests" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-md" value="<?php echo htmlspecialchars($_POST['guests'] ?? '1'); ?>">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Payment method <span class="text-red-500">*</span></label>
                <select name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <option value="">-- Select a method --</option>
                    <option value="Payment at the hotel" <?php if(isset($_POST['payment_method']) && $_POST['payment_method']==='Thanh toán tại khách sạn') echo 'selected'; ?>>Payment at the hotel</option>
                    <option value="Bank transfer" <?php if(isset($_POST['payment_method']) && $_POST['payment_method']==='Chuyển khoản ngân hàng') echo 'selected'; ?>>Bank transfer</option>
                    <option value="E-wallet" <?php if(isset($_POST['payment_method']) && $_POST['payment_method']==='Ví điện tử') echo 'selected'; ?>>E-wallet</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Notes</label>
                <textarea name="note" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="w-full bg-amber-600 text-white py-3 px-6 rounded-md hover:bg-amber-700 transition font-medium">Book a room</button>
        </form>
        <div class="text-center mt-8">
            <a href="main.php" class="text-amber-600 hover:underline">&larr; Return to homepage</a>
        </div>
    </div>
</body>
</html>