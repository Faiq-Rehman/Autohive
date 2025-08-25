<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $service_type = mysqli_real_escape_string($conn, $_POST['service_type']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);

    $query = "INSERT INTO paperwork_requests (name, email, phone, service_type, details)
              VALUES ('$name', '$email', '$phone', '$service_type', '$details')";

    if (mysqli_query($conn, $query)) {
        $success = "✅ Your request has been submitted successfully!";
    } else {
        $error = "❌ Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Paperwork Assistance - AutoHive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/index.css">
  <style>
    .paperwork-section {
      background-color: #f8f9fa;
      padding: 120px 0;
    }
    .highlight-box {
      background: white;
      border-left: 5px solid #dc3545;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
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

<section class="paperwork-section">
  <div class="container">
    <h2 class="mb-5 text-center text-danger">📄 Paperwork Assistance</h2>
    <p class="text-center mb-5">Avoid the hassle of legal procedures and let <strong>AutoHive</strong> manage your car's paperwork professionally and efficiently.</p>

    <?php if (isset($success)): ?>
      <div class="alert alert-success text-center"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
      <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <div class="highlight-box mb-5">
      <h5>Our Services Include:</h5>
      <ul class="mb-0">
        <li><strong>Ownership Transfer</strong> – Transfer the car to the buyer's name legally.</li>
        <li><strong>Vehicle Registration</strong> – Help registering or updating records.</li>
        <li><strong>Document Verification</strong> – CNIC, Smart Card, Token Tax & Excise details.</li>
        <li><strong>Tax & Token Payment</strong> – Assistance with vehicle taxes.</li>
        <li><strong>Legal Forms</strong> – Help with NOCs, affidavits, etc.</li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-8 offset-md-2">
        <h4 class="mb-3">💬 Request Paperwork Assistance</h4>
        <form action="" method="POST" class="row g-3">
          <div class="col-md-6">
            <input type="text" name="name" class="form-control" placeholder="Your Full Name" required>
          </div>
          <div class="col-md-6">
            <input type="email" name="email" class="form-control" placeholder="Your Email Address" required>
          </div>
          <div class="col-md-6">
            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
          </div>
          <div class="col-md-6">
            <select name="service_type" class="form-control" required>
              <option value="">Select Service</option>
              <option value="Ownership Transfer">Ownership Transfer</option>
              <option value="Registration">Registration</option>
              <option value="Document Verification">Document Verification</option>
              <option value="Tax Payment">Tax Payment</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-12">
            <textarea name="details" rows="4" class="form-control" placeholder="Tell us more about your request (optional)"></textarea>
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
