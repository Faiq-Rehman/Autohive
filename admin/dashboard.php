<?php
session_start();

// ✅ Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Redirect if not logged in as admin
if (!isset($_SESSION['admin'])) {
    echo "<script>
        alert('You must be logged in as an admin to access this page.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

include '../dbconnect.php';

// ✅ Function to check table before count
function getCount($conn, $table, $condition = '1=1') {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows == 0) {
        return 0;
    }
    $result = $conn->query("SELECT COUNT(*) AS total FROM $table");
    return $result->fetch_assoc()['total'];
}

$total_users = getCount($conn, "users", "user_type!='admin'");
$total_uservehicles = getCount($conn, "user_vehicles");
$total_adminvehicles = getCount($conn, "vehicles");
$total_transactions = getCount($conn, "transactions");
$issued_vehicles = getCount($conn, "vehicles", "status='issued'");
$total_requests = getCount($conn, "paperwork_requests");
$total_inspections = getCount($conn, "inspection_requests");
$total_contact = getCount($conn, "contact");
$total_reviews = getCount($conn, "reviews");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- ✅ SIDEBAR -->
    <div class="sidebar">
        <!-- ✅ LOGO AT TOP -->
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

        <!-- ✅ LOGOUT BUTTON AT BOTTOM -->
        <div class="logout-container">
            <a href="?logout=true" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- ✅ MAIN CONTENT -->
    <div class="main-content">

        <div class="row g-4">

            <!-- ✅ Total Users Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Total Users</h4>
                    <p><?php echo $total_users; ?></p>
                    <a href="manage_users.php" class="btn btn-outline-danger">Manage Users</a>
                </div>
            </div>

            <!-- ✅ Total user-Vehicles Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">User-Vehicles</h4>
                    <p><?php echo $total_uservehicles; ?></p>
                    <a href="manage_vehicle.php" class="btn btn-outline-danger">Manage User-Vehicles</a>
                </div>
            </div>

            <!-- ✅ Total admin-Vehicles Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Admin-Vehicles</h4>
                    <p><?php echo $total_adminvehicles; ?></p>
                    <a href="manage_admin-vehicles.php" class="btn btn-outline-danger">Manage Admin-Vehicles</a>
                </div>
            </div>

            <!-- ✅ Issued Vehicles Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Issued Admin-Vehicles</h4>
                    <p><?php echo $issued_vehicles; ?></p>
                    <a href="add_car.php" class="btn btn-outline-danger">Add Vehicle</a>
                </div>
            </div>
            
            <!-- ✅ Total contact Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Contacts</h4>
                    <p><?php echo $total_contact; ?></p>
                    <a href="manage_contact.php" class="btn btn-outline-danger">Manage Contacts</a>
                </div>
            </div>

            <!-- ✅ Total Transactions Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Transactions</h4>
                    <p><?php echo $total_transactions; ?></p>
                    <a href="manage_transaction.php" class="btn btn-outline-danger">Manage Transactions</a>
                </div>
            </div>

            <!-- ✅ Total Paperwork Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Paperwork-Requests</h4>
                    <p><?php echo $total_requests; ?></p>
                    <a href="paperwork_request.php" class="btn btn-outline-danger">Paperwork Requests</a>
                </div>
            </div>

            <!-- ✅ Total Inspections Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Inspections-Requests</h4>
                    <p><?php echo $total_inspections; ?></p>
                    <a href="inspection_request.php" class="btn btn-outline-danger">Inspection Requests</a>
                </div>
            </div>

            <!-- ✅ Total Reviews Card -->
            <div class="col-md-4">
                <div class="card">
                    <h4 class="fw-bold">Total Reviews</h4>
                    <p><?php echo $total_reviews; ?></p>
                    <a href="manage_reviews.php" class="btn btn-outline-danger">Manage Reviews</a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
