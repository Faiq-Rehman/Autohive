<?php
session_start();
include 'dbconnect.php';

// Fetch available brands for dropdown
$brands_result = $conn->query("SELECT DISTINCT brand FROM user_vehicles WHERE verified=1 AND status='available'");

$suggestions = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $budget = $_POST['budget'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];

    // Smart filtering logic
    $sql = "SELECT * FROM user_vehicles 
            WHERE verified=1 AND status='available' 
            AND price <= $budget";
    if ($category != "any") {
        $sql .= " AND category='$category'";
    }
    if ($brand != "any") {
        $sql .= " AND brand='$brand'";
    }
    $sql .= " ORDER BY price DESC LIMIT 5";

    $suggestions = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AI Car Match - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    .containers {
      padding-top: 100px;
      padding-bottom: 40px;
    }
    .card-img-top {
      object-fit: cover;
    }
    @media (max-width: 576px) {
      h3 {
        font-size: 1.5rem;
      }
    }
  </style>
  <link rel="stylesheet" href="css/footer.css" />
  <link rel="stylesheet" href="css/navbar.css" />
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container containers">
  <h3 class="text-center mb-4 text-dark">Car Match</h3>

  <!-- Search Form -->
  <div class="card p-4 mb-4 shadow-sm">
    <form method="POST">
      <div class="row g-3">
        <div class="col-md-4 col-sm-12">
          <label class="form-label fw-bold">Budget (PKR)</label>
          <input type="number" name="budget" class="form-control" placeholder="Enter budget" required>
        </div>

        <div class="col-md-4 col-sm-6">
          <label class="form-label fw-bold">Category</label>
          <select name="category" class="form-select">
            <option value="any">Any</option>
            <option value="new">New</option>
            <option value="used">Used</option>
            <option value="ev">EV</option>
          </select>
        </div>

        <div class="col-md-4 col-sm-6">
          <label class="form-label fw-bold">Brand</label>
          <input list="brand-options" name="brand" class="form-control" placeholder="Type or select brand">
          <!-- ya brand options show kar raha hai datalist main -->
          <datalist id="brand-options">
            <option>
            <?php while($row = $brands_result->fetch_assoc()) { ?>
              <option value="<?php echo $row['brand']; ?>">
            <?php } ?>
          </datalist>
        </div>

        <div class="col-12 d-grid mt-3">
          <button type="submit" class="btn btn-outline-primary">Find Match</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Car Results -->
  <div class="row">
    <?php if (!empty($suggestions) && $suggestions->num_rows > 0) {
      while ($car = $suggestions->fetch_assoc()) {
        $images = explode(",", $car['images']);
        $first_img = $images[0];
    ?>
    <div class="col-md-4 col-sm-6">
      <div class="card mb-4 shadow-sm h-100">
        <img src="uploads/<?php echo $first_img; ?>" class="card-img-top" height="200" alt="Car Image">
        <div class="card-body">
          <h5 class="card-title"><?php echo $car['name']; ?> - PKR <?php echo number_format($car['price']); ?></h5>
          <p class="card-text">
            <strong>Brand:</strong> <?php echo $car['brand']; ?><br>
            <strong>Category:</strong> <?php echo ucfirst($car['category']); ?>
          </p>
          <a href="view_vehicle.php?id=<?php echo $car['id']; ?>" class="btn btn-outline-primary w-100">View Details</a>
        </div>
      </div>
    </div>
    <?php } } else if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">
          No matches found. Try adjusting your filters.
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>