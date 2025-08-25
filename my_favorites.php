<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's favorite vehicles
$sql = "SELECT v.id, v.name, v.price, v.images 
        FROM favorites f
        JOIN user_vehicles v ON f.vehicle_id = v.id
        WHERE f.user_id = ? 
        ORDER BY f.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

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
<title>My Favorites - AutoHive</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/navbar.css">
<link rel="stylesheet" href="css/footer.css">
<style>
    .containers { padding-top: 90px; }
    .card img { height: 200px; object-fit: cover; }
</style>
</head>
<body class="bg-light">

<div class="container containers">
    <h2 class="mb-4 text-center">❤️ My Favorite Vehicles</h2>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $images = explode(",", $row['images']);
                $first_image = $images[0];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="uploads/<?php echo $first_image; ?>" class="card-img-top" alt="Vehicle">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text">PKR <?php echo formatPKR($row['price']); ?></p>
                        <a href="view_vehicle.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">View</a>
                        <a href="toggle_favorite.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No favorites yet. Go save some vehicles!</p>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>
</body>
</html>