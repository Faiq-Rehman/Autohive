<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';

function formatPKR($amount) {
  if ($amount >= 10000000) {
    return round($amount / 10000000, 1) . ' Crore';
  } elseif ($amount >= 100000) {
    return round($amount / 100000, 1) . ' Lacs';
  } else {
    return number_format($amount);
  }
}

function time_elapsed_string($datetime, $full = false) {
    if(!$datetime) return "just now";
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// Filters
$search = $_GET['search'] ?? '';
$city = $_GET['city'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Pagination setup
$limit = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch from user_vehicles
$user_query = "SELECT id, name, brand, model_year, fuel_type, transmission, mileage, engine_capacity, price, images, status, sold_at, registration_city, created_at, updated_at, 'user' AS source 
FROM user_vehicles WHERE verified = 1";

// Fetch from vehicles
$admin_query = "SELECT id, name, brand, model_year, fuel_type, transmission, mileage, engine_capacity, price, images, status, NULL AS sold_at, registration_city, created_at, updated_at, 'admin' AS source 
FROM vehicles WHERE verified = 1";

// Merge results
$all_vehicles = [];
$user_result = $conn->query($user_query);
if ($user_result) while ($row = $user_result->fetch_assoc()) $all_vehicles[] = $row;
$admin_result = $conn->query($admin_query);
if ($admin_result) while ($row = $admin_result->fetch_assoc()) $all_vehicles[] = $row;

// Apply Filters
$all_vehicles = array_filter($all_vehicles, function($car) use ($search, $city, $min_price, $max_price) {
  $match = true;
  if ($search && stripos($car['name'], $search) === false && stripos($car['brand'], $search) === false) {
    $match = false;
  }
  if ($city && stripos($car['registration_city'], $city) === false) {
    $match = false;
  }
  if ($min_price && $car['price'] < $min_price) {
    $match = false;
  }
  if ($max_price && $car['price'] > $max_price) {
    $match = false;
  }
  return $match;
});

// Separate available + sold
$available_vehicles = [];
foreach ($all_vehicles as $car) {
  if ($car['status'] == 'available') {
    $available_vehicles[] = $car;
  }
}

// Sort latest first
usort($available_vehicles, function($a, $b) {
  return strtotime($b['updated_at']) - strtotime($a['updated_at']);
});

// Total count + pagination
$total_available = count($available_vehicles);
$total_pages = ceil($total_available / $limit);
$available_vehicles = array_slice($available_vehicles, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>All Cars - AutoHive</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">
  <style>
    :root {
      --primary-red: #E0101B;
      --primary-red-dark: #c20c16;
    }
    
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('extraimages/carsbanner.jpg') center/cover no-repeat;
      padding: 100px 20px;
      color: white;
      text-align: center;
    }

    @media (max-width: 768px) {
      .hero {
        margin-top: 90px;
      }
    }
    
    /* Animation classes */
    .animate-fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    
    .animate-slide-up {
      animation: slideUp 0.8s ease-out;
    }
    
    .animate-delay-1 {
      animation-delay: 0.2s;
    }
    
    .animate-delay-2 {
      animation-delay: 0.4s;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes slideUp {
      from { 
        opacity: 0;
        transform: translateY(30px);
      }
      to { 
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Search Bar Styling */
    .search-bar {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      max-width: 950px;
      margin: 0 auto;
    }
    
    .search-bar input {
      border: none;
      outline: none;
      flex: 1;
      padding: 12px 18px;
      font-size: 15px;
      background: transparent;
    }
    
    .search-bar input::placeholder {
      color: #999;
    }
    
    .search-btn {
      background: var(--primary-red);
      color: #fff;
      border: none;
      padding: 12px 20px;
      transition: background 0.3s;
    }
    
    .search-btn:hover {
      background: var(--primary-red-dark);
    }
    
    /* Hide filters in responsive */
    @media (max-width: 768px) {
      .search-bar .extra-filter {
        display: none !important;
      }
    }
    
    /* Card Styling */
    .vehicle-card {
      display: flex;
      align-items: stretch;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      position: relative;
    }
    
    .vehicle-card:hover { 
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    
    .vehicle-card img {
      width: 380px;
      height: 200px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .vehicle-card:hover img {
      transform: scale(1.05);
    }
    
    .vehicle-info {
      flex: 1;
      padding: 12px 16px;
    }
    
    .vehicle-info h5 {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 6px;
    }
    
    .vehicle-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      font-size: 14px;
      color: #555;
      margin-bottom: 8px;
    }
    
    .vehicle-meta span {
      background: #f7f7f7;
      padding: 3px 8px;
      border-radius: 4px;
    }
    
    .extra-info {
      font-size: 15px;
      color: #000000ff;
      margin-top: 8px;
    }
    
    .side-actions {
      font-size: 15px;
      color: #000;
      margin-top: 15px;
    }
    
    .vehicle-side {
      width: 220px;
      background: #dcdcdcff;
      border-left: 1px solid #eee;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 15px;
      text-align: center;
    }
    
    .vehicle-price {
      font-size: 22px;
      font-weight: bold;
      color: #000000ff;
      margin-bottom: 8px;
    }
    
    .featured-badge {
      position: absolute;
      top: 8px;
      left: 8px;
      background: var(--primary-red);
      color: #fff;
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 3px;
      font-weight: 600;
      z-index: 10;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    /* Button styling with red color */
    .btn-primary {
      background-color: var(--primary-red);
      border-color: var(--primary-red);
    }
    
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
      background-color: var(--primary-red-dark);
      border-color: var(--primary-red-dark);
    }
    
    .btn-success {
      background-color: var(--primary-red);
      border-color: var(--primary-red);
    }
    
    .btn-success:hover, .btn-success:focus, .btn-success:active {
      background-color: var(--primary-red-dark);
      border-color: var(--primary-red-dark);
    }
    
    /* Pagination styling */
    .page-item.active .page-link {
      background-color: var(--primary-red);
      border-color: var(--primary-red);
    }
    
    .page-link {
      color: var(--primary-red);
    }
    
    .page-link:hover {
      color: var(--primary-red-dark);
    }
    
    /* Compare checkbox styling */
    input[type="checkbox"]:checked {
      background-color: var(--primary-red);
      border-color: var(--primary-red);
    }
    
    @media (max-width: 768px) {
      .vehicle-card { flex-direction: column; }
      .vehicle-card img { width: 100%; height: 200px; }
      .vehicle-side { width: 100%; border-left: none; border-top: 1px solid #eee; flex-direction: row; justify-content: space-around; }
    }
  </style>
</head>

<body style="background-color: white;">

    <!-- Hero Section -->
    <section class="hero animate__animated animate__fadeInDown">
        <div class="container">
            <h1 class="display-4 fw-bold">ALL CARS | AUTOHIVE</h1>
            <p class="lead">Your trusted digital showroom to explore, buy, sell, or trade vehicles with ease and
                confidence.</p>
        </div>
    </section>


  <div class="container py-5 mt-5">
    <form action="compare.php" method="GET">
      <div class="row gy-4">
        <?php if (!empty($available_vehicles)): ?>
        <?php 
          $animationDelay = 0;
          foreach ($available_vehicles as $car): 
          $images = explode(",", $car['images']);
          $first_image = !empty($images[0]) ? $images[0] : 'default_car.jpg';
          $animationDelay += 0.1;
        ?>
        <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: <?php echo $animationDelay; ?>s">
          <div class="vehicle-card">
            <div class="featured-badge">FEATURED</div>
            <img src="uploads/<?php echo htmlspecialchars($first_image); ?>" alt="Vehicle">
            <div class="vehicle-info">
              <h5><?php echo htmlspecialchars($car['name']); ?></h5>
              <div class="vehicle-meta">
                <span><?php echo htmlspecialchars($car['model_year']); ?></span>
                <span><?php echo htmlspecialchars($car['mileage']); ?> km</span>
                <span><?php echo htmlspecialchars($car['engine_capacity']); ?> cc</span>
                <span><?php echo htmlspecialchars($car['fuel_type']); ?></span>
                <span><?php echo htmlspecialchars($car['transmission']); ?></span>
              </div>
              <div class="extra-info"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($car['registration_city']); ?></div>
              <div class="extra-info">Updated <?php $last_update = $car['updated_at'] ?: $car['created_at']; echo time_elapsed_string($last_update); ?></div>
              <div class="side-actions">
                <input type="checkbox" name="compare[]" value="<?php echo $car['id'] . '-' . $car['source']; ?>"> Compare
              </div>
            </div>
            <div class="vehicle-side">
              <div class="vehicle-price">PKR <?php echo formatPKR($car['price']); ?></div>
              <a href="view_vehicle.php?id=<?php echo $car['id']; ?>&source=<?php echo $car['source']; ?>" class="btn btn-success btn-sm">View Details</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-center text-muted">No available vehicles found.</p>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <nav class="mt-4 animate__animated animate__fadeIn">
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
          <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a></li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
          <?php endfor; ?>

          <?php if ($page < $total_pages): ?>
          <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next &raquo;</a></li>
          <?php endif; ?>
        </ul>
      </nav>

      <div class="text-center mt-3 animate__animated animate__fadeIn">
        <button type="submit" class="btn btn-primary">Compare Selected</button>
      </div>
    </form>
  </div>

  <?php include 'footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Add intersection observer for scroll animations
    document.addEventListener('DOMContentLoaded', function() {
      const animatedElements = document.querySelectorAll('.animate__animated');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const animation = entry.target.getAttribute('data-animation');
            if (animation) {
              entry.target.classList.add(animation);
            }
            observer.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.1
      });
      
      animatedElements.forEach(el => {
        observer.observe(el);
      });
    });
  </script>
</body>
</html>