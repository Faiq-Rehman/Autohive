<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $model_year = $_POST['model_year'];
    $mileage = $_POST['mileage'];
    $fuel_type = $_POST['fuel_type'];
    $transmission = $_POST['transmission'];
    $condition = $_POST['condition'];
    $engine_capacity = $_POST['engine_capacity'];
    $color = $_POST['color'];
    $registration_city = $_POST['registration_city'];
    $features = trim($_POST['features']);
    $sell_type = $_POST['sell_type'];

    // Handle image upload
    $image_names = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = time() . '_' . basename($_FILES['images']['name'][$key]);
            $target_path = "uploads/" . $file_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                $image_names[] = $file_name;
            }
        }
    }
    $image_list = implode(",", $image_names);

    $stmt = $conn->prepare("INSERT INTO user_vehicles 
    (user_id, name, brand, price, category, description, images, model_year, mileage, fuel_type, transmission, `condition`, engine_capacity, color, registration_city, features, sell_type, verified, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'available')");
    $stmt->bind_param(
  "issdsssisssssssss",
  $user_id, 
  $name, 
  $brand, 
  $price, 
  $category, 
  $description, 
  $image_list, 
  $model_year, 
  $mileage, 
  $fuel_type, 
  $transmission, 
  $condition, 
  $engine_capacity, 
  $color, 
  $registration_city, 
  $features, 
  $sell_type
);
    
    if ($stmt->execute()) {
        $message = "Vehicle added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Vehicle - AutoHive</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .containers {
      padding-top: 60px;
    }
  </style>
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">
</head>
<body class="bg-light containers">
  <body class="bg-light containers">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="card shadow p-4">
          <h2 class="text-center mb-4">Add New Vehicle</h2>
          <?php if (!empty($message)): ?>
            <div class="alert alert-info text-center"><?php echo $message; ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Vehicle Name</label>
                <input type="text" class="form-control" name="name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" class="form-control" name="brand">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Price</label>
                <input type="number" class="form-control" name="price" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Category Type</label>
                <select class="form-select" name="category" required>
                  <option value="new">New</option>
                  <option value="used">Used</option>
                  <option value="ev">EV</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Model Year</label>
                <input type="number" class="form-control" name="model_year">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Mileage</label>
                <input type="number" class="form-control" name="mileage">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Fuel Type</label>
                <input type="text" class="form-control" name="fuel_type">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Transmission</label>
                <input type="text" class="form-control" name="transmission">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Condition</label>
                <input type="text" class="form-control" name="condition">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Engine Capacity</label>
                <input type="text" class="form-control" name="engine_capacity">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Color</label>
                <input type="text" class="form-control" name="color">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Registration City</label>
                <input type="text" class="form-control" name="registration_city">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Sell Type</label>
                <select class="form-select" name="sell_type">
                  <option value="sell">Sell</option>
                  <option value="trade">Trade</option>
                </select>
              </div>
              
              <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Features</label>
                <textarea class="form-control" name="features" rows="2"></textarea>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Upload Images</label>
                <input type="file" class="form-control" name="images[]" multiple>
              </div>
              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-danger">Add Vehicle</button>
              </div>
            </div>
          </form>
        </div>

        <div class="text-center mt-4">
          <a href="your_vehicles.php" class="btn btn-outline-dark">View Your Vehicles</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php include 'footer.php'; ?>
</body>
</html>
