<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'buyer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
  SELECT t.id, t.price, t.status, 
         COALESCE(v.name, uv.name) AS car_name, 
         s.name AS seller
  FROM transactions t
  JOIN users s ON t.seller_id = s.id
  LEFT JOIN vehicles v ON t.admin_vehicle_id = v.id
  LEFT JOIN user_vehicles uv ON t.user_vehicle_id = uv.id
  WHERE t.buyer_id = ?
  ORDER BY t.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchases = $stmt->get_result();

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
  <title>My Purchases - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .containers {
      padding: 80px 20px;
    }
    @media (max-width: 576px) {
      .containers {
        padding: 20px 10px;
      }
    }
  </style>
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">
</head>
<body class="bg-light">
<div class="container mt-4 containers">
  <h3 class="text-center mb-4">My Purchases</h3>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Car</th>
          <th>Seller</th>
          <th>Price</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($p = $purchases->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><?php echo $p['car_name']; ?></td>
          <td><?php echo $p['seller']; ?></td>
          <td>PKR <?php echo formatPKR($p['price']); ?></td>
          <td><?php echo ucfirst($p['status']); ?></td>
          <td>
            <?php if($p['status']=='completed') { ?>
              <a href="admin/show_receipt.php?id=<?php echo $p['id']; ?>" 
                 class="btn btn-success btn-sm" target="_blank">📄 Receipt</a>
            <?php } else { ?>
              <button class="btn btn-warning btn-sm" disabled>⏳ Pending</button>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <a href="index.php" class="btn btn-secondary">Back</a>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>