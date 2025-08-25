<?php
session_start();
include 'dbconnect.php';
include 'navbar.php';

// Format PKR
function formatPKR($amount) {
    if ($amount >= 10000000) {
        return round($amount / 10000000, 1) . ' Crore';
    } elseif ($amount >= 100000) {
        return round($amount / 100000, 1) . ' Lakh';
    } else {
        return number_format($amount);
    }
}

$compare_ids = $_GET['compare'] ?? [];
$vehicles_to_compare = [];

if (!empty($compare_ids)) {
    foreach ($compare_ids as $item) {
        list($id, $source) = explode('-', $item);

        if ($source === 'user') {
            $stmt = $conn->prepare("SELECT uv.id, uv.name, uv.brand, uv.model_year, uv.registration_city, uv.price, uv.verified, uv.images, uv.user_id AS seller_id, 'user' AS source 
                                    FROM user_vehicles uv
                                    WHERE uv.id = ? AND uv.verified = 1 AND uv.status = 'available'");
        } else {
            $stmt = $conn->prepare("SELECT v.id, v.name, v.brand, v.model_year, v.registration_city, v.price, v.verified, 'Admin' AS seller_name, 1 AS seller_verified, v.images, 'admin' AS source 
                                    FROM vehicles v
                                    WHERE v.id = ?");
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $vehicles_to_compare[] = $row;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Compare Vehicles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        .compare-table img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .containers {
            padding-top: 80px;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-4 containers">
    <h2 class="text-center fw-bold">Vehicle Comparison</h2>
    <br>
    <?php if (empty($vehicles_to_compare)): ?>
        <div class="alert alert-warning">No vehicles selected for comparison.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($vehicles_to_compare as $vehicle): ?>
                <?php
                // Default seller data
                $seller = [
                    'name' => $vehicle['seller_name'] ?? '',
                    'verified' => $vehicle['seller_verified'] ?? 0
                ];

                // If user vehicle → fetch from DB
                if ($vehicle['source'] === 'user') {
                    $seller_id = $vehicle['seller_id'];
                    $seller_query = $conn->prepare("SELECT name, verified FROM users WHERE id = ?");
                    $seller_query->bind_param("i", $seller_id);
                    $seller_query->execute();
                    $seller_result = $seller_query->get_result();
                    if ($seller_result->num_rows > 0) {
                        $seller = $seller_result->fetch_assoc();
                    }
                    $seller_query->close();
                }
                ?>
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h3><?= htmlspecialchars($vehicle['name']) ?> - <?= htmlspecialchars($vehicle['brand']) ?></h3>
                            <p><strong>Model Year:</strong> <?= htmlspecialchars($vehicle['model_year']) ?></p>
                            <p><strong>Price:</strong> <?= formatPKR($vehicle['price']) ?> PKR</p>
                            <p><strong>Registration City:</strong> <?= htmlspecialchars($vehicle['registration_city']) ?></p>
                            <p>
                                <strong>Seller:</strong> 
                                <?= htmlspecialchars($seller['name']) ?>
                                <?php if (!empty($seller['verified']) && $seller['verified'] == 1): ?>
                                    <span title="Verified Seller" style="color: green; font-size: 1.2em;">✔️</span>
                                <?php endif; ?>
                            </p>
                            <?php
                            $imagePath = '';
                            if (!empty($vehicle['images'])) {
                                if (is_string($vehicle['images']) && str_starts_with(trim($vehicle['images']), '[')) {
                                    $imgs = json_decode($vehicle['images'], true);
                                    if (!empty($imgs) && is_array($imgs)) {
                                        $imagePath = $imgs[0];
                                    }
                                } else {
                                    $imgs = explode(',', $vehicle['images']);
                                    $imagePath = trim($imgs[0]);
                                }
                            }
                            ?>
                            <?php if ($imagePath): ?>
                                <img src="uploads/<?= htmlspecialchars($imagePath) ?>" class="img-fluid rounded" alt="Vehicle Image">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a href="cars.php" class="btn btn-secondary mt-3">Back to Vehicles</a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>