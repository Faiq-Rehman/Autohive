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

// ✅ Verify user and auto-update their vehicles
if (isset($_GET['verify'])) {
    $id = intval($_GET['verify']);
    $conn->query("UPDATE users SET verified=1 WHERE id=$id");
    $conn->query("UPDATE vehicles SET verified=1, status='available' WHERE id=$id");
}

// ✅ Not Verify user and auto-update their vehicles
if (isset($_GET['notverify'])) {
    $id = intval($_GET['notverify']);
    $conn->query("UPDATE users SET verified=0 WHERE id=$id");
    $conn->query("UPDATE vehicles SET verified=0, status='pending' WHERE id=$id");
}

// ✅ Delete user and their vehicles
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // First delete all vehicles of the user
    $conn->query("DELETE FROM vehicles WHERE id = $id");

    // Then delete the user
    $conn->query("DELETE FROM users WHERE id = $id");
    
    // Optional: redirect to avoid repeated deletion on refresh
    header("Location: manage_users.php");
    exit();
}



// Fetch users (excluding admins)
$users = $conn->query("SELECT * FROM users WHERE user_type!='admin' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - AutoHive</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/manage_user.css">
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
    <h3 class="fw-bold text-center mb-4">Manage Users</h3>

    <!-- ✅ Search + Filter Row -->
    <div class="mb-3 text-center">
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-5">
            
            <!-- Filter Buttons -->
            <div class="btn-group gap-1">
                <button class="btn btn-outline-dark" onclick="filterUsers('all')">Show All</button>
                <button class="btn btn-outline-dark" onclick="filterUsers('buyer')">See Only Buyer</button>
                <button class="btn btn-outline-dark" onclick="filterUsers('seller')">See Only Seller</button>
            </div>

            <!-- Search Bar -->
            <div class="input-group w-auto">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by ID or Name...">
                <button class="btn btn-outline-primary" onclick="searchUsers()">Search</button>
                <button class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
            </div>

        </div>
    </div>


    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr class="text-center">
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Type</th>
          <th>Verified</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php while($u = $users->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $u['id']; ?></td>
          <td><?php echo $u['name']; ?></td>
          <td><?php echo $u['email']; ?></td>
          <td><?php echo ucfirst($u['user_type']); ?></td>
          <td><?php echo ((int)$u['verified'] === 1) ? "✅ Yes" : "❌ No"; ?></td>
          <td>
            <?php if ((int)$u['verified'] === 1) { ?>
              <a href="?notverify=<?php echo $u['id']; ?>" 
                 class="btn btn-toggle btn-sm"
                 onclick="return confirm('Mark this account as Not Verified? Vehicles will also be unverified.');">
                ❌ Not Verify
              </a>
            <?php } else { ?>
              <a href="?verify=<?php echo $u['id']; ?>" 
                 class="btn btn-toggle btn-sm"
                 onclick="return confirm('Mark this account as Verified? Vehicles will also be verified.');">
                ✅ Verify
              </a>
            <?php } ?>
            <a href="?delete=<?php echo $u['id']; ?>" 
               class="btn btn-outline-danger  btn-sm"
               onclick="return confirm('Delete this user and all their vehicles?');">
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
  function filterUsers(type) {
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
      const userType = row.children[3].textContent.toLowerCase(); // Type column
      if (type === 'all' || userType === type) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

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