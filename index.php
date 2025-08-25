<?php
session_start();
include 'dbconnect.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : '';
$user_type = $is_logged_in ? $_SESSION['user_type'] : '';

function formatPKR($amount) {
    if ($amount >= 10000000) {
        return round($amount / 10000000, 1) . ' Crore';
    } elseif ($amount >= 100000) {
        return round($amount / 100000, 1) . ' Lac';
    } else {
        return number_format($amount);
    }
}

$currentTime = time();
$threshold = strtotime('-30 minutes');

// ✅ Fetch only admin vehicles for featured section
$featured_result = $conn->query("SELECT * FROM vehicles WHERE verified=1 ORDER BY id DESC LIMIT 6");

// ✅ Fetch vehicles for sell/trade sections
$result_sell = $conn->query("SELECT * FROM vehicles WHERE verified=1 AND sell_type='sell' ORDER BY id DESC");
$result_trade = $conn->query("SELECT * FROM vehicles WHERE verified=1 AND sell_type='trade' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoHive - Drive the Future</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <style>
    :root {
      --primary: #E0101B;
      --secondary: #004aad;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #0ca33a;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    
    main {
      flex: 1;
    }
    
    /* Hero Section */
    .hero-section {
      background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1494976388531-d1058494cdd8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      height: 90vh;
      display: flex;
      align-items: center;
      position: relative;
    }
    
    .hero-content {
      max-width: 800px;
      margin: 0 auto;
      text-align: center;
    }
    
    .hero-badge {
      background: var(--primary);
      padding: 8px 20px;
      border-radius: 30px;
      font-size: 14px;
      display: inline-block;
      margin-bottom: 20px;
      color: #ffffffff;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 800;
      margin-bottom: 20px;
      line-height: 1.2;
      color: white;
    }
    
    .hero-subtitle {
      font-size: 1.2rem;
      margin-bottom: 30px;
      opacity: 0.9;
      color: white;
    }

    @media (max-width: 768px) {
      .hero-section {
        margin-top: 90px;
        height: 80vh;
      }
      .hero-title {
        font-size: 30px;
      }}
    
    /* Section Headers */
    .section-header {
      text-align: center;
      margin-bottom: 50px;
    }
    
    .section-title {
      font-weight: 800;
      position: relative;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    
    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: var(--primary);
    }
    
    .section-subtitle {
      color: #6c757d;
      max-width: 600px;
      margin: 0 auto;
    }
    
    /* Vehicle Cards - Fixed Styling */
    .vehicle-card {
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s ease;
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      height: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 30px; /* Added margin to accommodate pagination */
    }
    
    .vehicle-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    
    .vehicle-img-container {
      position: relative;
      height: 220px;
      overflow: hidden;
    }
    
    .vehicle-img {
      height: 100%;
      width: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .vehicle-card:hover .vehicle-img {
      transform: scale(1.05);
    }
    
    .vehicle-badge {
      position: absolute;
      top: 15px;
      left: 15px;
      background: var(--primary);
      color: white;
      padding: 5px 15px;
      border-radius: 30px;
      font-size: 12px;
      font-weight: 600;
      z-index: 1;
    }
    
    .card-body {
      padding: 20px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    
    .vehicle-title {
      font-weight: 700;
      font-size: 18px;
      margin-top: 10px;
      margin-bottom: 1px;
      color: var(--dark);
      height: 24px;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      margin-left: 10px;
    }
    
    .vehicle-details {
      color: #6c757d;
      font-size: 14px;
      margin-bottom: 1px;
      height: 20px;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
            margin-left: 10px;

    }
    
    .vehicle-price {
      color: var(--success);
      font-weight: 700;
      font-size: 18px;
      margin-bottom: 15px;
       margin-left: 10px;

    }
    
    .btn-view {
      background: var(--dark);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: 500;
      transition: all 0.3s;
      width: 100%;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: auto;
    }
    
    .btn-view:hover {
      background: var(--primary);
      transform: translateY(-2px);
    }
    
    /* Service Cards */
    .service-card {
      text-align: center;
      padding: 30px 20px;
      border-radius: 12px;
      background: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
      height: 100%;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      display: block;
    }
    
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      color: inherit;
      text-decoration: none;
    }
    
    .service-icon {
      width: 70px;
      height: 70px;
      background: rgba(224, 16, 27, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      color: var(--primary);
      font-size: 28px;
    }
    
    .service-title {
      font-weight: 700;
      margin-bottom: 15px;
      font-size: 18px;
    }
    
    .service-text {
      color: #6c757d;
    }
    
    /* Why Choose Us Cards */
    .feature-card {
      text-align: center;
      padding: 30px 20px;
      border-radius: 12px;
      background: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
      height: 100%;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      width: 70px;
      height: 70px;
      background: rgba(224, 16, 27, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      color: var(--primary);
      font-size: 28px;
    }
    
    .feature-title {
      font-weight: 700;
      margin-bottom: 15px;
      font-size: 18px;
    }
    
    .feature-text {
      color: #6c757d;
    }
    
    /* Promo Banner */
    .promo-banner {
      background: linear-gradient(to right, var(--secondary), var(--primary));
      border-radius: 15px;
      padding: 40px;
      color: white;
      margin: 60px 0;
    }
    
    .banner-title {
      font-weight: 800;
      margin-bottom: 20px;
      font-size: 2.2rem;
    }
    
    .btn-banner {
      background: white;
      color: var(--primary);
      border: none;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 100;
      transition: all 0.3s;
    }
    
    .btn-banner:hover {
      background: rgba(255, 255, 255, 0.9);
      transform: translateY(-3px);
    }
    
    /* Swiper Customization */
    .swiper {
      padding-bottom: 40px; /* Space for pagination */
    }
    
    .swiper-pagination {
      bottom: 0 !important; /* Force pagination to bottom */
    }
    
    .swiper-pagination-bullet {
      width: 12px;
      height: 12px;
      background-color: #ccc;
      opacity: 1;
    }
    
    .swiper-pagination-bullet-active {
      background-color: var(--primary);
    }
    
    .footer {
      background-color: #000;
      color: #ffffff;
      padding: 60px 20px 40px;
    }

    .footer h5 {
      color: #ffffff;
      margin-bottom: 20px;
      font-weight: 600;
      position: relative;
    }

    .footer h5::after {
      content: '';
      display: block;
      width: 40px;
      height: 3px;
      background-color: #E0101B;
      margin-top: 8px;
    }

    .footer a {
      color: #ffffff;
      text-decoration: none;
      display: block;
      margin-bottom: 10px;
      transition: 0.3s;
    }

    .footer a:hover {
      color: #E0101B;
    }

    .footer-logo img {
      width: 160px;
      margin-bottom: 15px;
    }

    .footer p {
      color: #ffffff;
    }

    .social-icons {
      display: flex;
      gap: 15px;
    }

    .footer .social-icons a {
      font-size: 20px;
      margin-right: 15px;
      color: #ffffff;
      transition: 0.3s;
    }

    .footer .social-icons a:hover {
      color: #E0101B;
    }

    .footer-bottom {
      text-align: center;
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #222;
      color: #ffffff;
      font-size: 14px;
    }

    .newsletter-input {
      background-color: #222;
      border: none;
    }

    .newsletter-input::placeholder {
      color: #ffffff;
    }

    .newsletter-input:focus {
      background-color: #333;
      color: #ffffff;
      border-color: #E0101B;
      box-shadow: none;
    }

    @media (max-width: 767px) {
      .footer .col-md-3 {
        margin-bottom: 30px;
      }
    }
  </style>
</head>

<body>
  <!-- Your Existing Navigation -->
  <?php include 'navbar.php'; ?>

  <main>
    <!-- Hero Section (Search Bar Removed) -->
    <section class="hero-section">
      <div class="container">
        <div class="hero-content">
          <span class="hero-badge">Your All-in-One Car Showroom</span>
          <h1 class="hero-title">Buy, Sell & Trade Cars<br>All in One Trusted Place</h1>
          <p class="hero-subtitle">Brand-new & Certified Pre-Owned Vehicles. Instant Evaluation. Secure Transactions.</p>
        </div>
      </div>
    </section>

    <!-- Our Services Section -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Our Services</h2>
          <p class="section-subtitle">Comprehensive automotive solutions for all your needs</p>
        </div>
        
        <div class="row g-4">
          <!-- Service 1 -->
          <div class="col-lg-4 col-md-6">
            <a href="services.php#car-buying" class="service-card">
              <div class="service-icon">
                <i class="fas fa-car"></i>
              </div>
              <h4 class="service-title">Car Buying</h4>
              <p class="service-text">Find your dream car from our extensive inventory of verified vehicles with complete transparency.</p>
            </a>
          </div>
          
          <!-- Service 2 -->
          <div class="col-lg-4 col-md-6">
            <a href="services.php#car-selling" class="service-card">
              <div class="service-icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
              <h4 class="service-title">Car Selling</h4>
              <p class="service-text">Sell your car quickly at the best price with our nationwide network of verified buyers.</p>
            </a>
          </div>
          
          <!-- Service 3 -->
          <div class="col-lg-4 col-md-6">
            <a href="services.php#car-trading" class="service-card">
              <div class="service-icon">
                <i class="fas fa-exchange-alt"></i>
              </div>
              <h4 class="service-title">Car Trading</h4>
              <p class="service-text">Exchange your current vehicle for a new one with our hassle-free trading process.</p>
            </a>
          </div>
          
          <!-- Service 4 -->
          <div class="col-lg-4 col-md-6">
            <a href="services.php#car-valuation" class="service-card">
              <div class="service-icon">
                <i class="fas fa-search-dollar"></i>
              </div>
              <h4 class="service-title">Car Valuation</h4>
              <p class="service-text">Get an accurate market valuation for your vehicle with our advanced pricing algorithm.</p>
            </a>
          </div>
          
          <!-- Service 5 -->
          <div class="col-lg-4 col-md-6">
            <a href="services.php#vehicle-inspection" class="service-card">
              <div class="service-icon">
                <i class="fas fa-tools"></i>
              </div>
              <h4 class="service-title">Vehicle Inspection</h4>
              <p class="service-text">Comprehensive 150-point inspection to ensure vehicle quality and reliability.</p>
            </a>
          </div>
          
          <!-- Service 6 -->
          <div class="col-lg-4 col-md-6">
            <a href="services.php#paperwork-assistance" class="service-card">
              <div class="service-icon">
                <i class="fas fa-file-contract"></i>
              </div>
              <h4 class="service-title">Paperwork Assistance</h4>
              <p class="service-text">Complete documentation support for smooth transfer of ownership and registration.</p>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Vehicles -->
    <section class="py-5">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Featured Vehicles</h2>
          <p class="section-subtitle">Discover our handpicked selection of premium vehicles</p>
        </div>
        
        <div class="swiper featuredSwiper">
          <div class="swiper-wrapper">
            <?php if ($featured_result->num_rows > 0): ?>
              <?php while($car = $featured_result->fetch_assoc()): 
                $images = explode(",", $car['images']);
                $first_image = $images[0];
              ?>
              <div class="swiper-slide">
                <div class="vehicle-card">
                  <div class="vehicle-img-container">
                    <img src="uploads/<?php echo $first_image; ?>" class="vehicle-img" alt="<?php echo $car['name']; ?>">
                    <span class="vehicle-badge">Featured</span>
                  </div>
                  <div class="card-body">
                    <h5 class="vehicle-title"><?php echo $car['name']; ?></h5>
                    <p class="vehicle-details"><?php echo $car['brand']; ?> | <?php echo $car['model_year']; ?> | <?php echo $car['registration_city']; ?></p>
                    <p class="vehicle-price">PKR <?php echo formatPKR($car['price']); ?></p>
                    <a href="view_vehicle.php?id=<?php echo $car['id']; ?>" class="btn btn-danger">View Details</a>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="col-12 text-center py-5">
                <h4>No featured cars available right now.</h4>
              </div>
            <?php endif; ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Why Choose AutoHive?</h2>
          <p class="section-subtitle">We provide the best car buying and selling experience</p>
        </div>
        
        <div class="row g-4">
          <!-- Feature 1 -->
          <div class="col-lg-4 col-md-6">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-car"></i>
              </div>
              <h4 class="feature-title">Wide Selection</h4>
              <p class="feature-text">From economy to luxury, find the perfect car for your needs and budget.</p>
            </div>
          </div>
          
          <!-- Feature 2 -->
          <div class="col-lg-4 col-md-6">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
              </div>
              <h4 class="feature-title">Verified Vehicles</h4>
              <p class="feature-text">All cars are thoroughly inspected and verified for quality assurance.</p>
            </div>
          </div>
          
          <!-- Feature 3 -->
          <div class="col-lg-4 col-md-6">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-handshake"></i>
              </div>
              <h4 class="feature-title">Trusted Sellers</h4>
              <p class="feature-text">Connect with reputable sellers for a safe and smooth transaction.</p>
            </div>
          </div>
          
          <!-- Feature 4 -->
          <div class="col-lg-4 col-md-6">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
              <h4 class="feature-title">Best Prices</h4>
              <p class="feature-text">Get the best value for your money with our competitive pricing.</p>
            </div>
          </div>
          
          <!-- Feature 5 -->
          <div class="col-lg-4 col-md-6">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-headset"></i>
              </div>
              <h4 class="feature-title">24/7 Support</h4>
              <p class="feature-text">Our customer support team is always ready to assist you.</p>
            </div>
          </div>
          
          <!-- Feature 6 -->
          <div class="col-lg-4 col-md-6">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-lock"></i>
              </div>
              <h4 class="feature-title">Secure Transactions</h4>
              <p class="feature-text">Enjoy safe and secure payment options for your convenience.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- For Sale Section -->
    <section class="py-5">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Cars For Sale</h2>
          <p class="section-subtitle">Browse our extensive collection of vehicles available for purchase</p>
        </div>
        
        <div class="swiper forSaleSwiper">
          <div class="swiper-wrapper">
            <?php 
            $result_sell->data_seek(0);
            $cars_sell = [];
            while($car = $result_sell->fetch_assoc()) {
                $is_sold = ($car['status'] === 'sold');
                $sold_recently = !$is_sold || (isset($car['sold_at']) && strtotime($car['sold_at']) >= $threshold);
                if ($car['sell_type'] === 'sell' && $sold_recently) {
                    $cars_sell[] = $car;
                }
            }
            if(count($cars_sell) > 0):
              foreach($cars_sell as $car): 
                $images = explode(",", $car['images']);
                $first_image = $images[0];
            ?>
            <div class="swiper-slide">
              <div class="vehicle-card">
                <div class="vehicle-img-container">
                  <img src="uploads/<?php echo $first_image; ?>" class="vehicle-img" alt="<?php echo $car['name']; ?>">
                  <span class="vehicle-badge">For Sale</span>
                  <?php if ($car['status'] === 'sold'): ?>
                  <span class="vehicle-badge" style="left: auto; right: 15px; background: #6c757d;">SOLD</span>
                  <?php endif; ?>
                </div>
                <div class="card-body">
                  <h5 class="vehicle-title"><?php echo $car['name']; ?></h5>
                  <p class="vehicle-details"><?php echo $car['brand']; ?> | <?php echo $car['model_year']; ?> | <?php echo $car['registration_city']; ?></p>
                  <p class="vehicle-price">PKR <?php echo formatPKR($car['price']); ?></p>
                  <a href="view_vehicle.php?id=<?php echo $car['id']; ?>" class="btn btn-danger">View Details</a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12 text-center py-5">
              <h4>No cars for sale right now.</h4>
            </div>
            <?php endif; ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
        
        <div class="text-center mt-5">
          <a href="cars.php?type=sell" class="btn btn-danger btn-lg">View All Vehicles</a>
        </div>
      </div>
    </section>

    <!-- Promo Banner -->
    <section class="py-5 bg-light">
      <div class="container">
        <div class="promo-banner">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h2 class="banner-title">Sell Your Car in Under 30 Minutes</h2>
              <p>Get instant valuation and sell your car quickly at the best price</p>
            </div>
            <div class="col-md-4 text-md-end">
              <a href="add_vehicle.php" class="btn btn-banner">Sell Your Car Now</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- For Trade Section -->
    <section class="py-5">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Cars For Trade</h2>
          <p class="section-subtitle">Find vehicles available for exchange or trade-in</p>
        </div>
        
        <div class="swiper forTradeSwiper">
          <div class="swiper-wrapper">
            <?php 
            $result_trade->data_seek(0);
            $cars_trade = [];
            while($car = $result_trade->fetch_assoc()) {
                $is_sold = ($car['status'] === 'sold');
                $sold_recently = !$is_sold || (isset($car['sold_at']) && strtotime($car['sold_at']) >= $threshold);
                if ($car['sell_type'] === 'trade' && $sold_recently) {
                    $cars_trade[] = $car;
                }
            }
            if(count($cars_trade) > 0):
              foreach($cars_trade as $car): 
                $images = explode(",", $car['images']);
                $first_image = $images[0];
            ?>
            <div class="swiper-slide">
              <div class="vehicle-card">
                <div class="vehicle-img-container">
                  <img src="uploads/<?php echo $first_image; ?>" class="vehicle-img" alt="<?php echo $car['name']; ?>">
                  <span class="vehicle-badge">For Trade</span>
                  <?php if ($car['status'] === 'sold'): ?>
                  <span class="vehicle-badge" style="left: auto; right: 15px; background: #6c757d;">SOLD</span>
                  <?php endif; ?>
                </div>
                <div class="card-body">
                  <h5 class="vehicle-title"><?php echo $car['name']; ?></h5>
                  <p class="vehicle-details"><?php echo $car['brand']; ?> | <?php echo $car['model_year']; ?> | <?php echo $car['registration_city']; ?></p>
                  <p class="vehicle-price">PKR <?php echo formatPKR($car['price']); ?></p>
                  <a href="view_vehicle.php?id=<?php echo $car['id']; ?>" class="btn btn-danger">View Details</a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12 text-center py-5">
              <h4>No cars for trade right now.</h4>
            </div>
            <?php endif; ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
        
        <div class="text-center mt-5">
          <a href="cars.php?type=trade" class="btn btn-danger btn-lg">View All Trade Vehicles</a>
        </div>
      </div>
    </section>
  </main>

<!-- Footer Start -->
 <?php include 'footer.php'  ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Initialize Swipers
    const featuredSwiper = new Swiper('.featuredSwiper', {
      slidesPerView: 1,
      spaceBetween: 20,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        992: {
          slidesPerView: 3,
        },
      },
    });
    
    const forSaleSwiper = new Swiper('.forSaleSwiper', {
      slidesPerView: 1,
      spaceBetween: 20,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        992: {
          slidesPerView: 4,
        },
      },
    });
    
    const forTradeSwiper = new Swiper('.forTradeSwiper', {
      slidesPerView: 1,
      spaceBetween: 20,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        992: {
          slidesPerView: 3,
        },
      },
    });
  </script>
</body>
</html>