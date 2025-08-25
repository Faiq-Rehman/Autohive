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

// ✅ Get vehicle ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid vehicle ID.'); window.location.href = 'manage_admin-vehicles.php';</script>";
    exit();
}

$vehicle_id = intval($_GET['id']);

// ✅ Fetch vehicle details
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? ");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    echo "<script>alert('Vehicle not found.'); window.location.href = 'manage_admin-vehicles.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = (int)($_POST['vehicle_id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $model_year = (int)($_POST['model_year'] ?? 0);
    $price = (float) str_replace(',', '', $_POST['price'] ?? 0);
    $mileage = (int)($_POST['mileage'] ?? 0);
    $fuel_type = $_POST['fuel_type'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $condition = $_POST['condition'] ?? '';
    $engine_capacity = $_POST['engine_capacity'] ?? '';
    $color = $_POST['color'] ?? '';
    $registration_city = $_POST['registration_city'] ?? '';
    $features = $_POST['features'] ?? '';
    $sell_type = $_POST['sell_type'] ?? 'sell';

    // Set verified as 1 explicitly
    $verified = 1;

    // Handle images
    $image_names = explode(',', $vehicle['images']);
    if (!empty($_FILES['images']['name'][0])) {
        $image_names = [];
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = time() . '_' . basename($_FILES['images']['name'][$key]);
            $target_path = "../uploads/" . $file_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                $image_names[] = $file_name;
            }
        }
    }
    $images_str = implode(',', $image_names);

    // Prepare update statement
    $stmt = $conn->prepare("
        UPDATE vehicles 
        SET name=?, brand=?, model_year=?, price=?, category=?, mileage=?, fuel_type=?, transmission=?, `condition`=?, engine_capacity=?, color=?, registration_city=?, sell_type=?, description=?, features=?, verified=?, images=? 
        WHERE id=?
    ");

    // Bind parameters
    // Types: s=string, i=int, d=double (float)
    $stmt->bind_param(
        "ssidsisssssssssisi",
        $name,
        $brand,
        $model_year,
        $price,
        $category,
        $mileage,
        $fuel_type,
        $transmission,
        $condition,
        $engine_capacity,
        $color,
        $registration_city, // fixed from $city
        $sell_type,
        $description,
        $features,
        $verified,
        $images_str,
        $vehicle_id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success'>✅ Vehicle updated successfully.</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>❌ Update failed: {$stmt->error}</div>";
    }

    header("Location: manage_admin-vehicles.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/edit_vehicles.css">
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

<div class="main-content">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="card shadow p-4">
          <h2 class="text-center mb-4">Update Vehicle</h2>

          <?php if (!empty($message)): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <!-- hidden field for vehicle id -->
            <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($vehicle['id']) ?>">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Vehicle Name</label>
                <input type="text" class="form-control" name="name" 
                  value="<?= htmlspecialchars($vehicle['name'] ?? '') ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" class="form-control" name="brand"
                  value="<?= htmlspecialchars($vehicle['brand'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Price</label>
                <input type="number" class="form-control" name="price" 
                  value="<?= htmlspecialchars($vehicle['price'] ?? '') ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Category Type</label>
                <select class="form-select" name="category" >
                  <option value="new" <?= (isset($vehicle['category']) && $vehicle['category']=='new') ? 'selected' : '' ?>>New</option>
                  <option value="used" <?= (isset($vehicle['category']) && $vehicle['category']=='used') ? 'selected' : '' ?>>Used</option>
                  <option value="ev" <?= (isset($vehicle['category']) && $vehicle['category']=='ev') ? 'selected' : '' ?>>EV</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Model Year</label>
                <input type="number" class="form-control" name="model_year"
                  value="<?= htmlspecialchars($vehicle['model_year'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Mileage</label>
                <input type="number" class="form-control" name="mileage"
                  value="<?= htmlspecialchars($vehicle['mileage'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Fuel Type</label>
                <input type="text" class="form-control" name="fuel_type"
                  value="<?= htmlspecialchars($vehicle['fuel_type'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Transmission</label>
                <input type="text" class="form-control" name="transmission"
                  value="<?= htmlspecialchars($vehicle['transmission'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Condition</label>
                <input type="text" class="form-control" name="condition"
                  value="<?= htmlspecialchars($vehicle['condition'] ?? '') ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Engine Capacity</label>
                <input type="text" class="form-control" name="engine_capacity"
                  value="<?= htmlspecialchars($vehicle['engine_capacity'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Color</label>
                <input type="text" class="form-control" name="color"
                  value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" >
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Registration City</label>
                <input type="text" class="form-control" name="registration_city"
                  value="<?= htmlspecialchars($vehicle['registration_city'] ?? '') ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Sell Type</label>
                <select class="form-select" name="sell_type" >
                  <option value="sell" <?= (isset($vehicle['sell_type']) && $vehicle['sell_type']=='sell') ? 'selected' : '' ?>>Sell</option>
                  <option value="trade" <?= (isset($vehicle['sell_type']) && $vehicle['sell_type']=='trade') ? 'selected' : '' ?>>Trade</option>
                </select>
              </div>

              <div class="col-12 mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($vehicle['description'] ?? '')  ?></textarea>
              </div>

              <div class="col-12 mb-3">
                <label class="form-label">Features</label>
                <textarea class="form-control" name="features" rows="2"><?= htmlspecialchars($vehicle['features'] ?? '')  ?></textarea>
              </div>

              <div class="col-12 mb-3">
                <label class="form-label">Upload Images</label>
                <input type="file" class="form-control" name="images[]" multiple >
                <small class="text-muted">Leave empty if you don't want to change images.</small>
              </div>

              <div class="col-12 d-grid">
                <button type="submit" class="btn btn-outline-danger">Update Vehicle</button>
              </div>
            </div>
          </form>
        </div>

        <div class="text-center back-btn mt-3">
          <a href="manage_admin-vehicles.php" class="btn btn-outline-dark">← Back</a>
        </div>
      </div>
    </div>
  </div>
</div>


</body>
</html>