<?php
session_start();
include '../dbconnect.php';

// ✅ Fetch vehicles added by this admin
$stmt = $conn->prepare("SELECT * FROM vehicles ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();


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

// ✅ Delete vehicle if requested
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $del_stmt->bind_param("i", $delete_id);
    $del_stmt->execute();
    header("Location: manage_admin-vehicles.php"); // Refresh after delete
    exit();
}


function formatPKR($amount) {
    if ($amount >= 10000000) {
        return round($amount / 10000000, 1) . ' Crore';
    } elseif ($amount >= 100000) {
        return round($amount / 100000, 1) . ' Lac';
    } else {
        return number_format($amount);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vehicles - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/manage_admin-vehicles.css">
</head>
<body>

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

        <h2 class="fw-bold text-center mb-4">Manage Your Vehicles</h2>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="add_car.php" class="btn btn-outline-success">+ Add New Vehicle</a>
        <div class="input-group input-group-md" style="max-width: 350px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by ID...">
            <button class="btn btn-outline-primary" onclick="searchUsers()">Search</button>
            <button class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
        </div>
    </div>

    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center table-striped">
        <thead class="table-dark">
            <tr>
                <th>Id</th>
                <th>Image</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Year</th>
                <th>Price</th>
                <th>Category</th>
                <th>Mileage</th>
                <th>Fuel</th>
                <th>Transmission</th>
                <th>Condition</th>
                <th>Engine Capacity</th>
                <th>Color</th>
                <th>City</th>
                <th>Discription</th>
                <th>Features</th>
                <th>Sell Type</th>
                <th>Verified</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $images = explode(",", $row['images']);
                    $image = (!empty($images[0])) ? $images[0] : 'default.png';

                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td><img src='../uploads/{$image}' alt='Car' style='width: 100px; height: auto;'></td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['brand']) . "</td>
                        <td>" . htmlspecialchars($row['model_year']) . "</td>
                        <td>" . formatPKR($row['price']) . "</td>
                        <td>" . htmlspecialchars($row['category']) . "</td>
                        <td>" . htmlspecialchars($row['mileage']) . " km</td>
                        <td>" . htmlspecialchars($row['fuel_type']) . "</td>
                        <td>" . htmlspecialchars($row['transmission']) . "</td>
                        <td>" . htmlspecialchars($row['condition']) . "</td>
                        <td>" . htmlspecialchars($row['engine_capacity']) . "</td>
                        <td>" . htmlspecialchars($row['color']) . "</td>
                        <td>" . htmlspecialchars($row['registration_city']) . "</td>
                        <td>" . nl2br(htmlspecialchars($row['description'])) . "</td>
                        <td>" . nl2br(htmlspecialchars($row['features'])) . "</td>
                        <td>" . htmlspecialchars($row['sell_type']) . "</td>
                        <td class='" . ($row['verified'] ? 'verified' : 'not-verified') . "'>" . ($row['verified'] ? 'Yes' : 'No') . "</td>
                        <td class='action-btns'>
                            <a href='edit_vehicles.php?id={$row['id']}' class='btn btn-sm btn-outline-primary'>Edit</a>
                            <br>
                            <a href='?delete={$row['id']}' onclick='return confirm(\"Are you sure?\")' class='btn btn-sm btn-outline-danger'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='18'>No vehicles found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<br>
        <a href="dashboard.php"><button class="btn btn-outline-dark">Back</button></a>

<script>
    function searchUsers() {
    const filter = document.getElementById("searchInput").value.toLowerCase().trim();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const id = row.children[0].textContent.toLowerCase();
        const name = row.children[1].textContent.toLowerCase();

        if (filter === "" || id === filter){
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
