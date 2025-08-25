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

// ✅ Delete Contact if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM contact WHERE id = ?");
    $del_stmt->bind_param("i", $delete_id);
    if ($del_stmt->execute()) {
        $msg = "<div class='alert alert-success'>✅ Message deleted successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>❌ Failed to delete message.</div>";
    }
}

// ✅ Fetch all contact messages
$contact_result = $conn->query("SELECT * FROM contact ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/manage_contact.css">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center mb-4">Manage Contacts</h2>
        <div class="input-group input-group-md" style="max-width: 350px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by ID or Name...">
            <button class="btn btn-outline-primary" onclick="searchUsers()">Search</button>
            <button class="btn btn-outline-secondary" onclick="resetSearch()">Reset</button>
        </div>
    </div>

    <?= $msg ?? '' ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center ">
            <thead class="table-dark">
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($contact_result->num_rows > 0) {
                    while ($row = $contact_result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['subject']}</td>
                            <td>" . nl2br(htmlspecialchars($row['message'])) . "</td>
                            <td>" . date('d-M-Y h:i A', strtotime($row['created_at'])) . "</td>
                            <td>
                                <a href='?delete={$row['id']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete this message?\")'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No contact messages found.</td></tr>";
                }
                ?>
            </tbody>

        </table>
    </div>

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
