<?php
session_start();
// Redirect if not logged in as admin
if (!isset($_SESSION['admin'])) {
    echo "<script>
        alert('You must be logged in as an admin to access this page.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// ✅ Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

include '../dbconnect.php';

// ✅ Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// ✅ Show message from session (after redirect)
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $category_id = $_POST['category_id'] ?? null; // Add this field in form if needed
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $model_year = $_POST['model_year'] ?? 0;
    $price = str_replace(',', '', $_POST['price'] ?? 0);
    $mileage = $_POST['mileage'] ?? 0;
    $fuel_type = $_POST['fuel_type'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $condition = $_POST['condition'] ?? '';
    $engine_capacity = $_POST['engine_capacity'] ?? '';
    $color = $_POST['color'] ?? '';
    $registration_city = $_POST['registration_city'] ?? '';
    $features = $_POST['features'] ?? '';
    $sell_type = $_POST['sell_type'] ?? 'sell';

    // ✅ Handle image uploads
    $image_names = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = time() . '_' . basename($_FILES['images']['name'][$key]);
            $target_path = "../uploads/" . $file_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                $image_names[] = $file_name;
            }
        }
    }
    $images_str = implode(',', $image_names);

    $stmt = $conn->prepare("INSERT INTO vehicles 
        ( name, brand, price, category, description, images, model_year, mileage, fuel_type, transmission, `condition`, engine_capacity, color, registration_city, features, sell_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssdssssissssssss", $name, $brand, $price, $category, $description, $images_str, $model_year, $mileage, $fuel_type, $transmission, $condition, $engine_capacity, $color, $registration_city, $features, $sell_type
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Vehicle added successfully!";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    header("Location: add_car.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Car - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/add_car.css">
</head>
<body>

<!-- ✅ SIDEBAR -->
<div class="sidebar">
  <div>
    <div class="logo">
      <img src="../extraimages/logo.png" alt="Logo">
    </div>
    <div class="sidebar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="manage_vehicle.php">Manage User-Vehicles</a>
                <a href="manage_admin-vehicles.php">Manage Admin-Vehicles</a>
                <a href="add_car.php">Add Admin-Vehicle</a>
                <a href="manage_transaction.php">Transactions</a>
                <a href="paperwork_request.php">Paperwork</a>
                <a href="inspection_request.php">Inspections</a>
                <a href="manage_contact.php">Contact</a>
                <a href="manage_reviews.php">Reviews</a>
            </div>
  </div>
  <div class="logout-container">
    <a href="?logout=true" class="logout-btn">Logout</a>
  </div>
</div>

<!-- ✅ MAIN CONTENT -->
<div class="main-content">
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
                <button type="submit" class="btn btn-outline-danger">Add Vehicle</button>
              </div>
            </div>
          </form>
        </div>

    <br>
    <a href="dashboard.php"><button class="btn btn-outline-dark">Back</button></a>

</div>



</body>
</html>
