<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Use Prepared Statement for Security
$stmt = $conn->prepare("
    SELECT t.*, v.name AS car_name, b.name AS buyer
    FROM transactions t
    JOIN user_vehicles v ON t.user_vehicle_id = v.id
    JOIN users b ON t.buyer_id = b.id
    WHERE t.seller_id = ?
    ORDER BY t.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sales = $stmt->get_result();

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Sales - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .containers{
      padding: 80px;
    }
  </style>
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">
</head>
<body class="bg-light">

<div class="container mt-5 containers">
  <h3 class="text-center mb-4 text-drak">My Sales</h3>

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Car</th>
          <th>Buyer</th>
          <th>Price</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($sales->num_rows > 0) { ?>
          <?php while($s = $sales->fetch_assoc()) { ?>
            <tr>
              <td><?php echo $s['id']; ?></td>
              <td><?php echo htmlspecialchars($s['car_name']); ?></td>
              <td><?php echo htmlspecialchars($s['buyer']); ?></td>
              <td>PKR <?php echo formatPKR($s['price']); ?></td>
              <td>
                <?php 
                  $status = strtolower($s['status']);
                  echo $status == 'completed' 
                       ? "<span class='badge bg-success'>Completed</span>"
                       : "<span class='badge bg-warning text-dark'>Pending</span>";
                ?>
              </td>
              <td>
                <?php if($status == 'completed') { ?>
                  <a href="admin/show_receipt.php?id=<?php echo $s['id']; ?>" 
                     class="btn btn-success btn-sm" target="_blank">📄 Receipt</a>
                <?php } else { ?>
                  <button class="btn btn-secondary btn-sm" disabled>⏳ Pending</button>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td colspan="6" class="text-center text-muted">No sales found yet.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
    <a href="index.php" class="btn btn-secondary"> Back</a>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>