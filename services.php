<?php
session_start();
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Services - AutoHive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/services.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">

  <style>
    /* Hero Animation */
    .hero {
      padding: 100px 20px;
      text-align: center;
      opacity: 0;
      transform: translateY(-100px);
      animation: slideDown 1.2s ease-out forwards;
      margin: 0;
    }

         @media (max-width: 768px) {
      .hero {
        margin-top: 90px;
      }}


    @keyframes slideDown {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="display-4">SERVICES | AUTOHIVE</h1>
            <p class="lead">Your trusted digital showroom to explore, buy, sell, or trade vehicles with ease and
                confidence.</p>
        </div>
    </section>

<div class="container services-section">
  <div class="row g-4 justify-content-center">
    <!-- Service Cards -->
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <a href="index.php#car-sell" class="stretched-link"></a>
        <div class="service-icon-circle"><span class="service-icon">🚗</span></div>
        <h5>Buy Vehicles</h5>
        <p>Browse verified cars and bikes, connect with trusted sellers, and purchase with confidence.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <div onclick="handleAddVehicle()" class="stretched-link" style="cursor: pointer;"></div>
        <div class="service-icon-circle"><span class="service-icon">💸</span></div>
        <h5>Sell Vehicles</h5>
        <p>List your car or bike, reach genuine buyers, and manage your sales easily through our platform.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <a href="index.php#car-trade" class="stretched-link"></a>
        <div class="service-icon-circle"><span class="service-icon">🔄</span></div>
        <h5>Trade Vehicles</h5>
        <p>Exchange your vehicle with others in a secure and transparent environment.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <a href="service_carMatch.php" class="stretched-link"></a>
        <div onclick="handleSellVehicle()" class="stretched-link" style="cursor: pointer;"></div>
        <div class="service-icon-circle"><span class="service-icon">🧩</span></div>
        <h5>Car Match</h5>
        <p>Get personalized recommendations and find the perfect vehicle for your needs.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <a href="vehicle_inspection.php" class="stretched-link"></a>
        <div class="service-icon-circle"><span class="service-icon">🔍</span></div>
        <h5>Vehicle Inspection</h5>
        <p>Professional inspection services to ensure your vehicle is in top condition before buying or selling.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <a href="paperwork.php" class="stretched-link"></a>
        <div class="service-icon-circle"><span class="service-icon">📄</span></div>
        <h5>Paperwork Assistance</h5>
        <p>We help you with all legal paperwork and ownership transfer for a hassle-free experience.</p>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="service-card p-4 text-center h-100 position-relative">
        <a href="contact.php" class="stretched-link"></a>
        <div class="service-icon-circle"><span class="service-icon">📞</span></div>
        <h5>Customer Support</h5>
        <p>Our team is available to answer your queries and provide support throughout your journey.</p>
      </div>
    </div>
  </div>
  <div class="cta-card mt-5">
    Ready to get started? <a href="signup.php">Create your account</a> or <a href="contact.php">Contact us</a> for more info!
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function handleAddVehicle() {
    const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    const userType = <?php echo json_encode($user_type); ?>;

    if (!isLoggedIn) {
      alert("You must be logged in to access this feature.");
      window.location.href = "login.php";
    } else if (userType === "buyer") {
      alert("You are logged in as a buyer. Sorry, you can't Sell vehicles. Only sellers can Sell vehicles.");
    } else if (userType === "seller") {
      window.location.href = "add_vehicle.php";
    }
  }

  function handleSellVehicle() {
    const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;
    const userType = <?php echo json_encode($user_type); ?>;

    if (!isLoggedIn) {
      alert("You must be logged in to access this feature.");
      window.location.href = "login.php";
    } else if (userType === "seller") {
      alert("You are logged in as a seller.Sorry, you can't use Car Match. Only buyers can use Car Match.");
    } else if (userType === "buyer") {
      window.location.href = "car_match.php";
    }
  }
</script>

</body>
</html>