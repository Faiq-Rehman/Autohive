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
    header("Location: ../index.php");
    exit();
}

// ✅ Delete vehicle if requested
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM paperwork_requests WHERE id = ?");
    $del_stmt->bind_param("i", $delete_id);
    $del_stmt->execute();
    header("Location: paperwork_request.php"); // Refresh after delete
    exit();
}


// ✅ Fetch paperwork requests
$query = "SELECT * FROM paperwork_requests ORDER BY submitted_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Paperwork Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/paperwork_request.css">
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
    <h3 class="fw-bold text-center mb-4">Manage Paperwork Requests</h3>
    <div class="input-group input-group-md" style="max-width: 350px;">
      <input type="text" id="searchInput" class="form-control" placeholder="Search by ID...">
      <button class="btn btn-outline-primary" onclick="searchUsers()">Search</button>
      <button class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
    </div>
</div>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Service Type</th>
              <th>Details</th>
              <th>Submitted At</th>
              <th>action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; while($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['service_type']) ?></td>
                <td><?= htmlspecialchars($row['details']) ?></td>
                <td><?= $row['submitted_at'] ?></td>
                <td><a href='?delete={$row[id]}' class='btn btn-sm btn-outline-danger' onclick='return confirm("Delete this message?")'>Delete</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-center text-muted">No paperwork requests yet.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-outline-dark mt-3">Back</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function searchUsers() {
    const filter = document.getElementById("searchInput").value.toLowerCase().trim();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const id = row.children[0].textContent.toLowerCase();
        const name = row.children[1].textContent.toLowerCase();

        if (filter === "" || id === filter) {
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
