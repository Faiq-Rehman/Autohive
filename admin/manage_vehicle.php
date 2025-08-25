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

include '../dbconnect.php';

// ✅ Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// ✅ Auto-sync: Update all vehicles based on seller verification
$conn->query("
    UPDATE user_vehicles v 
    JOIN users u ON v.user_id = u.id
    SET v.verified = IF(u.verified=1,1,0),
        v.status = IF(u.verified=1,'available','pending')
");

// ✅ Message variable
$message = "";

// ✅ Delete vehicle + related transactions
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete related transactions first (user or admin vehicles)
    $conn->query("DELETE FROM transactions WHERE user_vehicle_id=$id OR admin_vehicle_id=$id");

    // Delete the vehicle
    if ($conn->query("DELETE FROM user_vehicles WHERE id=$id") || $conn->query("DELETE FROM vehicles WHERE id=$id")) {
        $message = "✅ Vehicle (and related transactions) deleted successfully!";
    } else {
        $message = "❌ Error deleting vehicle: " . $conn->error;
    }
}

// ✅ Fetch vehicles with seller verification info
$vehicles = $conn->query("
    SELECT v.*, 
           u.name AS seller_name, 
           u.verified AS seller_verified,
           b.name AS buyer_name
    FROM user_vehicles v 
    JOIN users u ON v.user_id=u.id 
    LEFT JOIN transactions t ON v.id = t.user_vehicle_id
    LEFT JOIN users b ON t.buyer_id = b.id
    ORDER BY v.id DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Vehicles - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/manage_vehicle.css">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold text-center mb-4">Manage Vehicles</h3>
      <div class="input-group input-group-md" style="max-width: 350px;">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by ID or Name...">
        <button class="btn btn-outline-primary" onclick="searchUsers()">Search</button>
        <button class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
      </div>
    </div>

    <?php if(!empty($message)) { ?>
      <div class="alert alert-info text-center"><?php echo $message; ?></div>
    <?php } ?>

    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Brand</th>
          <th>Seller</th>
          <th>Buyer</th>
          <th>Seller Verified</th>
          <th>Vehicle Verified</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($v = $vehicles->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $v['id']; ?></td>
          <td><?php echo $v['name']; ?></td>
          <td><?php echo $v['brand']; ?></td>
          <td><?php echo $v['seller_name']; ?></td>
          <td><?php echo $v['buyer_name']; ?></td>
          <td><?php echo $v['seller_verified'] ? "✅" : "❌"; ?></td>
          <td><?php echo $v['verified'] ? "✅" : "❌"; ?></td>
          <td><?php echo ucfirst($v['status']); ?></td>
          <td>
            <a href="?delete=<?php echo $v['id']; ?>" 
               class="btn btn-outline-danger btn-sm" 
               onclick="return confirm('Delete vehicle and related transactions?');">
               Delete
            </a>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-outline-dark">Back</a>
  </div>
<script>
    function searchUsers() {
    const filter = document.getElementById("searchInput").value.toLowerCase().trim();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const id = row.children[0].textContent.toLowerCase();
        const name = row.children[1].textContent.toLowerCase();

        if (filter === "" || id === filter || name.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function resetSearch() {
    document.getElementById("searchInput").value = "";
    document.querySelectorAll("tbody tr").forEach(row => row.style.display = '');
}
</script>
</body>
</html>