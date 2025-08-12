<?php
session_start();
// Get data from GET, if not present, leave it blank
$username = isset($_GET['username']) ? $_GET['username'] : '';
$password = isset($_GET['password']) ? $_GET['password'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$confirm_password = isset($_GET['confirm_password']) ? $_GET['confirm_password'] : '';
if ($username == '' || $password == '' || $email == '') {
echo "";
} elseif ($password == $confirm_password) {
echo "check your password";
} else {
    header("location:login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxStay - Book premium hotels</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            scroll-behavior: smooth;
        }
        
        .room-card:hover .room-img {
            transform: scale(1.05);
        }
        
        .room-img {
            transition: transform 0.3s ease;
        }
        
        .testimonial-card {
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .amenity-icon {
            transition: transform 0.3s ease;
        }
        
        .amenity-item:hover .amenity-icon {
            transform: scale(1.2);
        }
    </style>
</head>
<body class="bg-gray-50">
        <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="#" class="text-2xl font-bold text-amber-600 flex items-center">
                    <i class="fas fa-hotel mr-2"></i>
                    LuxStay
                </a>
            </div>
            
            <nav class="hidden md:flex space-x-8">
                <a href="#" class="text-gray-800 hover:text-amber-600 font-medium">Home</a>
                <a href="#rooms" class="text-gray-800 hover:text-amber-600 font-medium">Rooms</a>
                <a href="#amenities" class="text-gray-800 hover:text-amber-600 font-medium">Amenities</a>
                <a href="#about" class="text-gray-800 hover:text-amber-600 font-medium">About Us</a>
                <a href="#contact" class="text-gray-800 hover:text-amber-600 font-medium">Contact</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="hidden md:flex items-center space-x-2">
                        <span class="text-gray-800">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="logout.php" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="register/login.html" class="hidden md:block px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition">
                        Log in
                    </a>
                <?php endif; ?>
                <button class="md:hidden text-gray-800 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

        <section class="relative h-[600px] bg-gray-900 overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-50 z-10"></div>
        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/c706fb32-e012-4fdd-bb1c-1d5009237075.png" alt="A luxurious resort with an infinity pool overlooking the sea and distant mountains" class="w-full h-full object-cover">
        
        <div class="absolute inset-0 flex items-center z-20">
            <div class="container mx-auto px-4">
                <div class="max-w-2xl">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Your perfect getaway starts at LuxStay</h1>
                    <p class="text-gray-200 text-lg mb-8">Discover top-class resort experiences with 5-star service and modern amenities</p>
                </div>
                
                        <div class="bg-white rounded-lg shadow-xl p-6 max-w-4xl mt-8">
                    <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="searchForm">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Location</label>
                            <div class="relative">
                                <input type="text" name="location" placeholder="City, location..." value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <i class="fas fa-map-marker-alt absolute right-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Check-in</label>
                            <div class="relative">
                                <input type="date" name="check_in" value="<?php echo htmlspecialchars($_GET['check_in'] ?? ''); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <i class="fas fa-calendar-alt absolute right-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Check-out</label>
                            <div class="relative">
                                <input type="date" name="check_out" value="<?php echo htmlspecialchars($_GET['check_out'] ?? ''); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <i class="fas fa-calendar-alt absolute right-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Guests</label>
                            <div class="relative">
                                <select name="guests" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <?php for($i=1;$i<=5;$i++): ?>
                                    <option value="<?php echo $i; ?>" <?php if(isset($_GET['guests']) && $_GET['guests']==$i) echo 'selected'; ?>><?php echo $i; ?> guests</option>
                                    <?php endfor; ?>
                                    <option value="6" <?php if(isset($_GET['guests']) && $_GET['guests']==6) echo 'selected'; ?>>5+ guests</option>
                                </select>
                            </div>
                        </div>
                        <div class="md:col-span-4">
                            <button type="submit" class="w-full mt-2 bg-amber-600 text-white py-3 px-6 rounded-md hover:bg-amber-700 transition font-medium">
                                Find rooms
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

        <section id="about" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0 md:pr-8">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/fa662589-43d8-41b0-9f93-1b238831fd6f.png" alt="LuxStay hotel's luxurious reception hall with modern architecture and warm lighting" class="rounded-lg shadow-lg w-full">
                </div>
                
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">LuxStay Hotel - Class and Amenities</h2>
                    <p class="text-gray-600 mb-6">LuxStay Hotel offers you a high-class resort experience with a system of comfortable rooms, attentive service, and a luxurious atmosphere. We are committed to providing our guests with the utmost comfort and convenience.</p>
                    
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="flex items-start">
                            <div class="bg-amber-100 p-3 rounded-full mr-4">
                                <i class="fas fa-concierge-bell text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1">24/7 Service</h4>
                                <p class="text-gray-600 text-sm">Our staff is always ready to serve you</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-amber-100 p-3 rounded-full mr-4">
                                <i class="fas fa-wifi text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1">High-speed Wifi</h4>
                                <p class="text-gray-600 text-sm">Unlimited internet connection</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-amber-100 p-3 rounded-full mr-4">
                                <i class="fas fa-utensils text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1">Diverse Restaurants</h4>
                                <p class="text-gray-600 text-sm">Rich and diverse cuisine</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-amber-100 p-3 rounded-full mr-4">
                                <i class="fas fa-swimming-pool text-amber-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1">Outdoor Pool</h4>
                                <p class="text-gray-600 text-sm">Infinity pool with city view</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#rooms" class="inline-block px-6 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition font-medium">
                        Learn more
                    </a>
                </div>
            </div>
        </div>
    </section>

        <section id="rooms" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Rooms</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">LuxStay offers a variety of rooms from standard to luxury suites, meeting all our guests' needs</p>
            </div>
            
            <?php
            include_once "connect.php";
            // Process room search
            $where = ["available=1"];
            $searchByAddress = false;
            if (!empty($_GET['location'])) {
                $loc = mysqli_real_escape_string($conn, $_GET['location']);
                // If an address is entered, prioritize searching by address
                $where[] = "(address LIKE '%$loc%' OR room_name LIKE '%$loc%' OR short_description LIKE '%$loc%')";
                $searchByAddress = true;
            }
            if (!empty($_GET['guests'])) {
                $guests = intval($_GET['guests']);
                if ($guests <= 5) {
                    $where[] = "capacity >= $guests";
                } else {
                    $where[] = "capacity >= 5";
                }
            }
            $sql = "SELECT * FROM rooms WHERE " . implode(' AND ', $where) . " ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0):
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while($room = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden room-card">
                    <div class="overflow-hidden">
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>" class="w-full h-64 object-cover room-img">
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($room['room_name']); ?></h3>
                            <span class="text-amber-600 font-bold">$<?php echo number_format($room['price']); ?></span>
                        </div>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($room['short_description']); ?></p>
                        <div class="flex flex-wrap gap-2 mb-2">
                        <div class="mb-4 text-sm text-gray-500 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-amber-500"></i>
                            <?php echo htmlspecialchars($room['address'] ?? ''); ?>
                        </div>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?php echo $room['capacity']; ?> guests</span>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?php echo $room['size']; ?>m²</span>
                            <?php if($room['has_wifi']): ?><span class="text-xs bg-gray-100 px-2 py-1 rounded">Wifi</span><?php endif; ?>
                            <?php if($room['has_bathtub']): ?><span class="text-xs bg-gray-100 px-2 py-1 rounded">Bathtub</span><?php endif; ?>
                            <?php if($room['has_balcony']): ?><span class="text-xs bg-gray-100 px-2 py-1 rounded">Balcony</span><?php endif; ?>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex text-amber-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <a href="booking.php" class="text-amber-600 hover:text-amber-700 font-medium flex items-center">
                                Book now <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php elseif (!empty($_GET['location'])): ?>
                <div class="text-center text-red-500 font-semibold py-8">No rooms are currently available at this location.</div>
            <?php endif; ?>
            
            <div class="text-center mt-12">
                <a href="allroom.php" class="inline-block px-6 py-3 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition font-medium">
                    View all rooms
                </a>
            </div>
        </div>
    </section>

        <section id="amenities" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Amenities & Services</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">LuxStay offers a system of premium amenities to give you the most perfect vacation experience</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div class="bg-gray-50 p-6 rounded-lg text-center amenity-item">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-amber-600 text-2xl amenity-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Restaurants</h3>
                    <p class="text-gray-600">3 restaurants serving diverse Asian and European cuisine</p>
                </div>
                
                        <div class="bg-gray-50 p-6 rounded-lg text-center amenity-item">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-spa text-amber-600 text-2xl amenity-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Spa & Massage</h3>
                    <p class="text-gray-600">Premium spa services to help you relax and rejuvenate</p>
                </div>
                
                        <div class="bg-gray-50 p-6 rounded-lg text-center amenity-item">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dumbbell text-amber-600 text-2xl amenity-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Gym</h3>
                    <p class="text-gray-600">Modern fitness room with full equipment</p>
                </div>
                
                        <div class="bg-gray-50 p-6 rounded-lg text-center amenity-item">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-swimming-pool text-amber-600 text-2xl amenity-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Swimming Pool</h3>
                    <p class="text-gray-600">Infinity pool with panoramic city views</p>
                </div>
            </div>
        </div>
    </section>

        <section class="py-16 bg-amber-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">What our guests say about us</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Genuine feedback from customers who have experienced services at LuxStay</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white p-6 rounded-lg shadow-md testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5ec10ff5-bc38-43d0-ada8-327707a8de10.png" alt="Portrait of a smiling middle-aged man in a suit" class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">Mr. Nguyen Van A</h4>
                            <div class="flex text-amber-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Wonderful hotel with attentive service. Clean rooms and full amenities. The staff is enthusiastic and professional. Will definitely come back!"</p>
                </div>
                
                        <div class="bg-white p-6 rounded-lg shadow-md testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/6db89997-e2c7-4dea-9d11-b775f4173b29.png" alt="Portrait of a young, long-haired woman smiling in a relaxed setting" class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">Ms. Tran Thi B</h4>
                            <div class="flex text-amber-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"First time experiencing LuxStay's service and I'm truly impressed. The restaurant is delicious, the pool is beautiful. The view from the suite room is especially wonderful at night!"</p>
                </div>
                
                        <div class="bg-white p-6 rounded-lg shadow-md testimonial-card">
                    <div class="flex items-center mb-4">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/fb7c7ac5-19c1-4944-828f-c50ea1631682.png" alt="An elderly man with a satisfied expression in a luxurious setting" class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">Mr. Le Van C</h4>
                            <div class="flex text-amber-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"I've stayed at many 5-star hotels, but LuxStay truly stands out. The spa service is excellent, the rooms are spacious and comfortable. Highly recommend experiencing it!"</p>
                </div>
            </div>
        </div>
    </section>

        <section class="py-16 bg-gray-900">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready for an amazing vacation?</h2>
            <p class="text-gray-300 max-w-2xl mx-auto mb-8">Book today to receive special offers and experience premium service from LuxStay</p>
            <a href="allroom.php" class="inline-block px-8 py-3 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition font-medium text-lg">
                Book now
            </a>
        </div>
    </section>

        <footer id="contact" class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-hotel mr-2"></i> LuxStay
                    </h3>
                    <p class="text-gray-400 mb-4">LuxStay - Class in every detail, luxury in every moment</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Rooms</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">News</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Booking</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Spa & Massage</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Restaurant</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Events</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Offers</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-amber-400"></i>
                            <span class="text-gray-400">123 ABC Street, XYZ District, Ho Chi Minh City</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-2 text-amber-400"></i>
                            <span class="text-gray-400">0123 456 789</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-amber-400"></i>
                            <span class="text-gray-400">info@luxstay.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>© 2023 LuxStay. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.md\\:hidden').addEventListener('click', function() {
            const nav = document.querySelector('nav');
            nav.classList.toggle('hidden');
            nav.classList.toggle('flex');
            nav.classList.toggle('flex-col');
            nav.classList.toggle('absolute');
            nav.classList.toggle('top-16');
            nav.classList.toggle('left-0');
            nav.classList.toggle('right-0');
            nav.classList.toggle('bg-white');
            nav.classList.toggle('p-4');
            nav.classList.toggle('shadow-md');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Form validation for booking
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkIn = this.querySelector('input[type="date"]');
            const today = new Date().toISOString().split('T')[0];
            if (checkIn.value < today) {
                e.preventDefault();
                alert('Please select a check-in date from today onwards');
                checkIn.focus();
            }
        });

        // Scroll to rooms section after search submit
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            setTimeout(function() {
                const roomsSection = document.getElementById('rooms');
                if (roomsSection) {
                    roomsSection.scrollIntoView({ behavior: 'smooth' });
                }
            }, 100); // delay to allow page reload with results
        });
    </script>

</body>
</html>