<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

// Handle form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $location = mysqli_real_escape_string($conn, $_POST['location']);
  $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
  $notes = mysqli_real_escape_string($conn, $_POST['notes']);

  $insert = "INSERT INTO inspection_requests (name, email, phone, location, vehicle, notes)
             VALUES ('$name', '$email', '$phone', '$location', '$vehicle', '$notes')";

  if (mysqli_query($conn, $insert)) {
    $success = "✅ Your inspection request has been submitted!";
  } else {
    $error = "❌ Something went wrong. Try again later.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vehicle Inspection - AutoHive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/footer.css">
  <style>
    body {
      background-color: #f9f9f9;
    }
    .inspection-section {
      padding: 100px 0;
    }
    .highlight-box {
      background: white;
      padding: 25px;
      border-left: 5px solid #dc3545;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      margin-bottom: 40px;
    }
    .form-control, .btn {
      border-radius: 10px;
    }
    .btn-submit {
      background-color: black;
      color: white;
    }
    .btn-submit:hover {
      background-color: #dc3545;
      color: white;
    }
  </style>
</head>
<body>

<section class="inspection-section">
  <div class="container">
    <h2 class="text-center text-danger mb-4">🔍 Vehicle Inspection Service</h2>
    <p class="text-center mb-5">Want to buy or sell a vehicle but unsure about its condition? <strong>AutoHive</strong> offers a professional vehicle inspection service, so you can make informed decisions with confidence.</p>

    <?php if (isset($success)): ?>
      <div class="alert alert-success text-center"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
      <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <div class="highlight-box">
      <h5>What We Inspect:</h5>
      <ul class="mb-0">
        <li><strong>Engine Health</strong> – Smoke, leaks, sound, temperature</li>
        <li><strong>Body Condition</strong> – Dents, rust, repaint, frame damage</li>
        <li><strong>Suspension & Brakes</strong> – Road test and brake check</li>
        <li><strong>Interior & Electronics</strong> – Lights, windows, A/C, multimedia</li>
        <li><strong>Chassis Number</strong> – Match with documents</li>
        <li><strong>Excise & Tax Status</strong> – Verify via official records</li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-8 offset-md-2">
        <h4 class="mb-3">📩 Request Inspection</h4>
        <form action="" method="POST" class="row g-3">
          <div class="col-md-6">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
          </div>
          <div class="col-md-6">
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
          </div>
          <div class="col-md-6">
            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
          </div>
          <div class="col-md-6">
            <input type="text" name="location" class="form-control" placeholder="Your City / Location" required>
          </div>
          <div class="col-12">
            <input type="text" name="vehicle" class="form-control" placeholder="Car/Bike Details (Make, Model, Year)" required>
          </div>
          <div class="col-12">
            <textarea name="notes" rows="4" class="form-control" placeholder="Any special concerns or notes (optional)"></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-submit w-100">Submit Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
