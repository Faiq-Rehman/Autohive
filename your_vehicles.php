<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM user_vehicles WHERE user_id = $user_id ORDER BY id DESC");

// ✅ Delete review if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM user_vehicles WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $delete_id, $user_id);
    if ($del_stmt->execute()) {
        $msg = "<div class='alert alert-success'>✅ Review deleted successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>❌ Failed to delete review.</div>";
    }
}

function formatPKR($amount) {
    if ($amount >= 10000000) return round($amount / 10000000, 1) . ' Crore';
    if ($amount >= 100000) return round($amount / 100000, 1) . ' Lac';
    return number_format($amount);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Your Vehicles - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/footer.css">
  <style>
    .vehicle-card {
      border: 1px solid #dee2e6;
      border-radius: 12px;
      overflow: hidden;
      background-color: #ffffff;
      transition: all 0.3s ease-in-out;
    }
    .vehicle-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }
    .vehicle-card img {
      height: 190px;
      object-fit: cover;
    }
    .card-body { padding: 15px; }
    .status-pending { color: #fd7e14; font-weight: 600; }
    .status-approved { color: #28a745; font-weight: 600; }
    .d-flex{
      gap: 5px;
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-5 mt-5">
  <h3 class="text-center mb-4 text-dark">Your Added Vehicles</h3>
  <div class="row">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $images = explode(",", $row['images']);
            $status = $row['verified'] == 1 ? "<span class='status-approved'>Approved</span>" : "<span class='status-pending'>Pending</span>";
            echo '
              <div class="col-md-4 mb-4">
                <div class="card vehicle-card p-2">
                  <img src="uploads/'.$images[0].'" class="card-img-top" alt="'.$row['name'].'">
                  <div class="card-body">
                    <h5 class="card-title">'.htmlspecialchars($row['name']).'</h5>
                    <p class="card-text">
                      <strong>Brand:</strong> '.htmlspecialchars($row['brand']).'<br>
                      <strong>Sell Type:</strong> <span class="fst-italic">'.ucfirst($row['sell_type']).'</span><br>
                      <strong>Price:</strong> PKR '.formatPKR($row['price']).'<br>
                      <strong>Category:</strong> '.htmlspecialchars($row['category']).'<br>
                      <strong>Status:</strong> '.$status.'
                    </p>
                    <div class="d-flex">
                      <a href="view_vehicle.php?id='.$row['id'].'" class="btn btn-outline-primary w-50 mt-2">View</a>
                      <a href="edit_vehicles.php?id='.$row['id'].'" class="btn btn-outline-primary w-50 mt-2">Edit</a>
                      <a href="?delete='.$row['id'].'" class="btn btn-outline-danger w-50 mt-2" 
                        onclick="return confirm(\'Are you sure you want to delete this vehicle?\')">Delete</a>
                    </div>
                  </div>
                </div>
              </div>';
        }
        }
    else {
        echo "<p class='text-center text-muted'>You haven't added any vehicles yet.</p>";
    }
    ?>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>