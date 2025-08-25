<?php
session_start();
include '../dbconnect.php';

// Optional: Sirf admin access
if (!isset($_SESSION['admin'])) {
    die("Access denied");
}

// Fetch reviews with seller and buyer info
$sql = "
    SELECT 
        r.id,
        r.rating,
        r.comment,
        r.created_at,
        u.name AS buyer_name,
        CASE 
            WHEN uv.id IS NOT NULL THEN su.name
            ELSE 'Admin'
        END AS seller_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    LEFT JOIN user_vehicles uv ON uv.id = r.vehicle_id
    LEFT JOIN users su ON uv.user_id = su.id
    ORDER BY r.created_at DESC
";
$result = $conn->query($sql);

// ✅ Delete review if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $del_stmt->bind_param("i", $delete_id);
    if ($del_stmt->execute()) {
        $msg = "<div class='alert alert-success'>✅ Review deleted successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>❌ Failed to delete review.</div>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/manage_reviews.css">
</head>
<body class="bg-light">

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
<div class="main-content">
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Reviews</h2>
        <div class="input-group input-group-md" style="max-width: 350px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by ID or Name...">
            <button class="btn btn-outline-primary" onclick="searchUsers()">Search</button>
            <button class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
        </div>
    </div>


    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Seller Name</th>
                <th>Buyer Name</th>
                <th>Stars</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['seller_name']) ?></td>
                    <td><?= htmlspecialchars($row['buyer_name']) ?></td>
                    <td>
                        <?php for ($i=1; $i<=5; $i++): ?>
                            <span style="color:<?= $i <= $row['rating'] ? 'gold' : 'lightgray' ?>">★</span>
                        <?php endfor; ?>
                    </td>
                    <td><?= htmlspecialchars($row['comment']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this review?')">Delete</a>
                    </td>

                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No reviews found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-outline-dark mt-3">Back</a>
</div>
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