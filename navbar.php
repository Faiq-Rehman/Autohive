<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'dbconnect.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_type = $is_logged_in ? $_SESSION['user_type'] : '';
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$user_image = $is_logged_in && isset($_SESSION['user_image']) ? $_SESSION['user_image'] : 'default.png';

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_image);
    $stmt->fetch();
    $stmt->close();
    
    if ($current_image) {
        $_SESSION['user_image'] = $current_image;
        $user_image = $current_image;
    }
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM chats WHERE receiver_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$has_vehicles = false;
if ($is_logged_in && $user_type === 'seller') {
    $checkVehicles = $conn->prepare("SELECT id FROM user_vehicles WHERE user_id = ? LIMIT 1");
    $checkVehicles->bind_param("i", $_SESSION['user_id']);
    $checkVehicles->execute();
    $checkVehicles->store_result();
    if ($checkVehicles->num_rows > 0) {
        $has_vehicles = true;
    }
}

// Get current page name for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"/>
    <link rel="stylesheet" href="navbar.css"/>
    <style>
        /* NAVBAR STYLING */
        .navbar {
            background-color: #000 !important;
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease-in-out;
        }
        
        .nav-link:hover {
            color: #E0101B !important;
        }
        
        .navbar-nav > .nav-item:not(.dropdown) > .nav-link::before {
            content: "";
            position: absolute;
            bottom: 1px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            visibility: hidden;
            background-color: white;
            transition: 0.3s ease-in-out;
        }
        
        .navbar-nav > .nav-item:not(.dropdown) > .nav-link:hover::before {
            width: 100%;
            visibility: visible;
        }
        
        .nav-link.active {
            color: #E0101B !important;
        }
        
        .nav-link.active::before {
            width: 100%;
            visibility: visible;
            background-color: #E0101B;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute !important;
            background-color: #fff;
            z-index: 1050;
        }
        
        .dropdown-menu.show {
            display: block !important;
        }
        
        @media (min-width: 992px) {
            .nav-item.dropdown:hover .dropdown-menu {
                display: block;
            }
        }
        
        /* SEARCH BAR STYLING */
        .search-container {
            background-color: #000;
            padding: 15px 0;
            border-bottom: 1px solid #333;
        }
        
        .search-form {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }
        
        .search-form input {
            padding: 8px 12px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #000;
            color: white;
            flex: 1;
        }
        
        .search-form input::placeholder {
            color: #aaa;
        }
        
        .search-form button {
            background-color: #E0101B;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .search-form button:hover {
            background-color: #c00d17;
        }
        
        .search-toggle-container {
            display: flex;
            align-items: center;
            margin-left: auto;
            gap: 8px;
        }
        
        .search-icon-mobile {
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        /* WHITE TOGGLE BUTTON */
        .navbar-toggler {
            border: 1px solid #666;
            padding: 0.25rem 0.5rem;
            color: white !important;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* PROFILE SIDEBAR */
        .profile-sidebar {
            position: fixed;
            top: 0;
            right: -320px;
            width: 300px;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.3);
            z-index: 2000;
            transition: right 0.3s ease-in-out;
            overflow-y: auto;
        }
        
        .profile-sidebar.active {
            right: 0;
        }
        
        /* BOTTOM NAVBAR */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #000;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1030;
        }
        
        .bottom-nav a {
            color: #fff;
            text-decoration: none;
            text-align: center;
            font-size: 12px;
            flex: 1;
            padding: 8px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .bottom-nav a.active {
            color: #E0101B;
        }
        
        .bottom-nav a i {
            font-size: 18px;
            margin-bottom: 3px;
        }
        
        /* RED SELL BUTTON WITH PROPER CIRCLE */
        .sell-btn {
            width: 45px;
            height: 45px;
            background-color: #E0101B;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 2px 8px rgba(224, 16, 27, 0.5);
        }
        
        .sell-btn:hover {
            background-color: #c00d17;
            color: #fff;
        }
        
        .sell-label {
            color: white;
            font-size: 12px;
            margin-top: 3px;
            text-align: center;
        }
        
        .sell-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .off-link {
            color: #fff;
            display: block;
            padding: 10px 0;
            text-decoration: none;
        }
        
        .bottom-nav .badge {
            font-size: 0.6rem;
            padding: 4px 6px;
        }
        
        /* RESPONSIVE STYLING */
        @media (max-width: 991px) {
            .navbar .collapse, 
            .navbar .btn, 
            .navbar .dropdown {
                display: none !important;
            }
            
            .search-container {
                display: none;
            }
            
            .search-toggle-container {
                display: flex !important;
            }
            
            .search-offcanvas .offcanvas-body {
                background-color: #000;
                color: white;
                padding: 20px;
            }
            
            .search-offcanvas input {
                background-color: #000;
                color: white;
                border: 1px solid #444;
                margin-bottom: 10px;
            }
            
            .search-offcanvas input::placeholder {
                color: #aaa;
            }
            
            .mobile-search-form {
                display: flex;
                flex-direction: column;
            }
            
            /* Prevent body scrolling when search offcanvas is open */
            body.offcanvas-open {
                overflow: hidden;
                position: fixed;
                width: 100%;
            }
        }
        
        @media (min-width: 992px) {
            .bottom-nav {
                display: none;
            }
            
            .search-toggle-container {
                display: none !important;
            }
        }
        
        /* OFFCANVAS STYLING */
        .offcanvas-end {
            width: 100% !important;
            background-color: #000 !important;
            z-index: 1050;
        }
        
        .offcanvas-header h2 {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }
        
        .off-link {
            display: block;
            padding: 12px 0;
            font-size: 16px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid #333;
        }
        
        .off-link:hover {
            color: #E0101B;
            text-decoration: none;
        }
        
        /* Custom search toggle button */
        .custom-search-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 0.25rem 0.5rem;
            margin-right: 10px;
        }
        
        .dual-toggle-container {
            display: flex;
            align-items: center;
        }

      @media (max-width: 768px) {
      .navbar-toggler {
         display: none;
      }}
      


    </style>
</head>
<body>
    <!-- ✅ UNIFIED RESPONSIVE NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-black py-3 fixed-top px-3 px-md-5">
        <div class="container-fluid">
            <!-- 🔹 Logo: Always left -->
            <a class="navbar-brand" href="index.php">
                <img src="extraimages/logo.png" alt="Logo" style="height: 50px;">
            </a>
            
            <!-- 🔹 Toggle for Mobile (Search removed from top) -->
            <div class="search-toggle-container">
                <!-- Search icon button -->
                <button class="custom-search-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#searchOffcanvas">
                    <i class="fas fa-search"></i>
                </button>
                <!-- Menu toggle button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#moreMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <!-- 🔹 Center links for desktop -->
            <div class="collapse navbar-collapse justify-content-center d-none d-lg-flex" id="navbarCollapse">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'cars.php') ? 'active' : ''; ?>" href="cars.php">All Cars</a></li>
                    <?php if ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white <?php echo (in_array($current_page, ['index.php', 'add_vehicle.php', 'your_vehicles.php'])) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">Marketplace</a>
                        <ul class="dropdown-menu">
                            <?php if ($user_type === 'buyer'): ?>
                            <li><a class="dropdown-item" href="index.php#car-sell">Buy</a></li>
                            <?php endif; ?>
                            <?php if ($user_type === 'seller'): ?>
                            <li><a class="dropdown-item" href="add_vehicle.php">Sell</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="index.php#car-trade">Trade</a></li>
                            <?php if ($user_type === 'seller' && $has_vehicles): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="your_vehicles.php">See Your Vehicles</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if ($user_type === 'seller'): ?>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'my_sales.php') ? 'active' : ''; ?>" href="my_sales.php">Sale's</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'chat.php') ? 'active' : ''; ?>" href="chat.php">Chat</a></li>
                    <?php endif; ?>
                    <?php if ($user_type === 'buyer'): ?>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'my_purchases.php') ? 'active' : ''; ?>" href="my_purchases.php">Purchases's</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'car_match.php') ? 'active' : ''; ?>" href="car_match.php">Car Match</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'ev_tools.php') ? 'active' : ''; ?>" href="ev_tools.php">Ev Tools</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link text-white <?php echo ($current_page == 'help.php') ? 'active' : ''; ?>" href="help.php">Help</a></li>
                </ul>
            </div>
            
            <!-- 🔹 User Profile Section (Right side) -->
            <div class="d-flex align-items-center">
                <?php if ($is_logged_in): ?>
                <div class="text-white text-end ms-2" id="userProfileBtn" style="cursor:pointer;">
                    <!-- 🖼 Profile image -->
                    <img src="admin/pic/<?php echo htmlspecialchars($current_image); ?>" alt="Profile" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                    <!-- 👤 Username below image in mobile, beside in desktop -->
                    <div class="d-none d-md-inline ms-1"><?php echo ucfirst($user_name); ?></div>
                    <div class="d-block d-md-none small text-center"><?php echo ucfirst($user_name); ?></div>
                </div>
                <?php else: ?>
                <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="signup.php" class="btn" style="background-color: #E0101B; color: #fff;">Signup</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- ✅ SEARCH BAR (Desktop Only) -->
    <div class="search-container d-none d-lg-block mt-5 pt-5">
        <div class="container">
            <form class="search-form" action="cars.php" method="GET">
                <input type="text" name="search" placeholder="Search for vehicles..." class="form-control" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <input type="text" name="city" placeholder="City" class="form-control" value="<?php echo isset($_GET['city']) ? htmlspecialchars($_GET['city']) : ''; ?>">
                
                <input type="number" name="min_price" placeholder="Min Price" class="form-control" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                
                <input type="number" name="max_price" placeholder="Max Price" class="form-control" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
    
    <!-- ✅ MOBILE SEARCH OFFCANVAS -->
    <div class="offcanvas offcanvas-top search-offcanvas" tabindex="-1" id="searchOffcanvas" aria-labelledby="searchOffcanvasLabel">
        <div class="offcanvas-header bg-black">
            <h5 class="offcanvas-title text-white" id="searchOffcanvasLabel">Search Vehicles</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body bg-black">
            <form class="mobile-search-form" action="cars.php" method="GET">
                <input type="text" name="search" placeholder="Search for vehicles..." class="form-control" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <input type="text" name="city" placeholder="City" class="form-control" value="<?php echo isset($_GET['city']) ? htmlspecialchars($_GET['city']) : ''; ?>">
                
                <input type="number" name="min_price" placeholder="Min Price" class="form-control" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                
                <input type="number" name="max_price" placeholder="Max Price" class="form-control" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                
                <button type="submit" class="btn btn-danger w-100 mt-2">Search</button>
            </form>
        </div>
    </div>
    
    <!-- ✅ PROFILE SIDEBAR -->
    <?php if ($is_logged_in): ?>
    <div id="profileSidebar" class="profile-sidebar d-flex flex-column">
        <div>
            <div class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="mb-0">👤 User Profile</h5>
                <button id="closeSidebarBtn" class="btn btn-sm btn-danger">&times;</button>
            </div>
            <div class="text-center p-3">
                <img src="admin/pic/<?php echo htmlspecialchars($current_image); ?>" alt="Profile" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover;"><br><br>
                <h6 class="mb-0">UserName: <?php echo ucfirst($user_name); ?></h6>
                <h6 class="text-muted">User-type: <?php echo ucfirst($user_type); ?></h6>
            </div>
            <div class="sidebar-content px-3">
                <ul class="list-unstyled">
                    <?php if ($user_type === 'buyer'): ?>
                    <li><a href="my_favorites.php" class="btn btn-secondary w-100 pt-1">Favorite Vehicles</a></li>
                    <br>
                    <?php endif; ?>
                    <li><a href="edit_profile.php" class="btn btn-secondary w-100 pt-1">Edit Profile</a></li>
                </ul>
            </div>
        </div>
        <div class="px-3 pb-3 mt-auto">
            <a href="logout.php" class="btn btn-danger w-100">Logout</a>
        </div>
    </div>
    <div id="sidebarBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040;"></div>
    <?php endif; ?>
    
    <!-- ✅ BOTTOM NAVBAR (Mobile Only) - UPDATED ORDER -->
    <nav class="bottom-nav d-lg-none">
        <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="cars.php" class="<?php echo ($current_page == 'cars.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-car-side"></i>
            <span>Cars</span>
        </a>
        <div class="sell-container" onclick="SellVehicle()">
            <a href="<?php echo $is_logged_in ? 'add_vehicle.php' : 'login.php'; ?>" class="sell-btn">
                <i class="fa-solid fa-plus"></i>
            </a>
            <div class="sell-label">Sell</div>
        </div>
        <a href="services.php" class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-check"></i>
            <span>Services</span>
        </a>
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#moreMenu" class="<?php echo ($current_page == 'more') ? 'active' : ''; ?>">
            <i class="fas fa-bars"></i>
            <span>More</span>
        </a>
    </nav>
    
    <!-- ✅ OFFCANVAS MORE MENU -->
    <div class="offcanvas offcanvas-end text-white" tabindex="-1" id="moreMenu" style="background-color: #000;">
        <div class="offcanvas-header border-bottom">
            <h2 class="text-white mx-auto mb-0 fw-bold">Menu</h2>
            <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <?php if (!$is_logged_in): ?>
            <div class="d-flex gap-2 mb-3">
                <a href="login.php" class="btn btn-outline-light flex-fill">Login</a>
                <a href="signup.php" class="btn flex-fill" style="background-color: #E0101B; color: #fff;">Signup</a>
            </div>
            <?php endif; ?>
            
            <!-- Search option in More menu -->
            <div class="mb-3">
                <a href="#" class="off-link d-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#searchOffcanvas" data-bs-dismiss="offcanvas">
                    <i class="fas fa-search me-2"></i> Search
                </a>
            </div>
            
            <ul class="list-unstyled">
                <?php if ($is_logged_in): ?>
                <li class="off-link">
                    <a class="text-white" data-bs-toggle="collapse" href="#marketplaceMenu" role="button" aria-expanded="false" aria-controls="marketplaceMenu">Marketplace</a>
                    <ul class="collapse list-unstyled ps-3 mt-2" id="marketplaceMenu">
                        <?php if ($user_type === 'buyer'): ?>
                        <li><a class="off-link" href="index.php#car-sell">Buy</a></li>
                        <?php endif; ?>
                        <?php if ($user_type === 'seller'): ?>
                        <li><a class="off-link" href="add_vehicle.php">Sell</a></li>
                        <?php endif; ?>
                        <li><a class="off-link" href="index.php#car-trade">Trade</a></li>
                        <?php if ($user_type === 'seller' && $has_vehicles): ?>
                        <li><a class="off-link" href="your_vehicles.php">See Your Vehicles</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if ($user_type === 'seller'): ?>
                <li><a class="off-link" href="my_sales.php">Sale's</a></li>
                <li><a class="off-link" href="chat.php">Chat</a></li>
                <?php endif; ?>
                <?php if ($user_type === 'buyer'): ?>
                <li><a class="off-link" href="my_purchases.php">Purchases's</a></li>
                <li><a class="off-link" href="car_match.php">Car Match</a></li>
                <?php endif; ?>
                <li><a class="off-link" href="ev_tools.php">Ev Tools</a></li>
                <li><a class="off-link" href="contact.php">Contact</a></li>
                <li><a class="off-link" href="about.php">About Us</a></li>
                <li><a class="off-link" href="help.php">Help</a></li>
            </ul>
        </div>
    </div>
    
    <!-- ✅ SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const userProfileBtn = document.getElementById('userProfileBtn');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const profileSidebar = document.getElementById('profileSidebar');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            
            // ✅ function toggleSidebar
            function toggleSidebar() {
                profileSidebar.classList.toggle('active');
                sidebarBackdrop.style.display = profileSidebar.classList.contains('active') ? 'block' : 'none';
            }
            
            userProfileBtn?.addEventListener('click', toggleSidebar);
            sidebarBackdrop?.addEventListener('click', toggleSidebar);
            closeSidebarBtn?.addEventListener('click', toggleSidebar);
            
            // Prevent body scrolling when search offcanvas is open
            const searchOffcanvas = document.getElementById('searchOffcanvas');
            searchOffcanvas.addEventListener('show.bs.offcanvas', function () {
                document.body.classList.add('offcanvas-open');
            });
            searchOffcanvas.addEventListener('hidden.bs.offcanvas', function () {
                document.body.classList.remove('offcanvas-open');
            });
            
            // Ensure search icon and toggle button are always visible
            const searchToggleContainer = document.querySelector('.search-toggle-container');
            if (searchToggleContainer) {
                searchToggleContainer.style.display = 'flex';
            }
        });
        
        function SellVehicle() {
            const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
            const userType = '<?php echo $user_type; ?>';
            
            if (!isLoggedIn) {
                window.location.href = "login.php";
            } else if (userType === "buyer") {
                alert('Only sellers can access the Sell page. Create account as seller to access this page.');
            } else if (userType === "seller") {
                window.location.href = "add_vehicle.php";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>