<?php
session_start();
include 'dbconnect.php';

// PHPMailer include
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

// Gmail credentials
$your_email    = "team.autohive@gmail.com";
$app_password  = "skxmawjuptvgccws"; // ✅ without spaces

include 'navbar.php';

$user_id = $_SESSION['user_id'];
$vehicle_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch vehicle data
$sql = "SELECT * FROM user_vehicles WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $vehicle_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger text-center'>Vehicle not found or you don't have permission to edit it.</div>";
    exit();
}

$vehicle = $result->fetch_assoc();

$alert_message = "";
$alert_type = "";

// Update vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model_year = $_POST['model_year'];
    $mileage = $_POST['mileage'];
    $fuel_type = $_POST['fuel_type'];
    $transmission = $_POST['transmission'];
    $condition = $_POST['condition'];
    $engine_capacity = $_POST['engine_capacity'];
    $color = $_POST['color'];
    $registration_city = $_POST['registration_city'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $features = $_POST['features'];

    // Old price from DB
    $old_price = $vehicle['price'];

    // Update query
    $update_sql = "UPDATE user_vehicles 
                   SET name=?, brand=?, model_year=?, mileage=?, fuel_type=?, transmission=?, `condition`=?, 
                       engine_capacity=?, color=?, registration_city=?, category=?, price=?, description=?, features=? 
                   WHERE id=? AND user_id=?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param(
      'ssiisssssssdssii',
      $name, $brand, $model_year, $mileage, $fuel_type,
      $transmission, $condition, $engine_capacity, $color,
      $registration_city, $category, $price, $description,
      $features, $vehicle_id, $user_id
    );
    $stmt_update->execute();

    $alert_message = "Vehicle updated successfully.";
    $alert_type = "success";

    // ✅ Price drop check here
    if ($price < $old_price) {
        $fav_sql = "SELECT u.id, u.email, u.name 
                    FROM favorites f 
                    JOIN users u ON f.user_id = u.id 
                    WHERE f.vehicle_id = ?";
        $stmt_fav = $conn->prepare($fav_sql);
        $stmt_fav->bind_param("i", $vehicle_id);
        $stmt_fav->execute();
        $fav_result = $stmt_fav->get_result();

        while ($user = $fav_result->fetch_assoc()) {
            $msg = "

                We are pleased to inform you that the price of \"{$name}\" has been revised.

                - Previous Price: PKR " . number_format($old_price) . "
                - Updated Price: PKR " . number_format($price) . "

                We value your continued trust and look forward to serving you.

                Best regards,
                <b>AutoHive Team</b><br>
                AutoHive – Drive the Future
                ";

            // ✅ Sirf email bhejna hai
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $your_email;
                $mail->Password   = $app_password;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom($your_email, 'AutoHive');
                $mail->addAddress($user['email'], $user['name']);

                $mail->isHTML(true);
                $mail->Subject = "Price Drop Alert - AutoHive";
                $mail->Body    = "
                    <p>Hello <b>{$user['name']}</b>,</p>
                    <p>{$msg}</p>
                    <p><a href='http://localhost/autohive/view_vehicle.php?id={$vehicle_id}'>View Vehicle</a></p>
                    <br><p>Regards,<br>Team AutoHive</p>
                ";
                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
            }
        }

        // Overwrite alert if drop detected
        $alert_message = "Vehicle updated and price drop alerts sent to buyers!";
        $alert_type = "info";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Vehicle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .containers{
            padding-top: 110px;
        }
    </style>
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body class="containers">
    <div class="container mt-5 card">

    <h2 class="mb-4">Edit Vehicle</h2>

    <?php if (!empty($alert_message)): ?>
        <div class="alert alert-<?= $alert_type ?> text-center"><?= $alert_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3"><label>Vehicle Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($vehicle['name']) ?>" required>
        </div>
        <div class="mb-3"><label>Brand</label>
            <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($vehicle['brand']) ?>" required>
        </div>
        <div class="mb-3"><label>Model Year</label>
            <input type="text" name="model_year" class="form-control" value="<?= htmlspecialchars($vehicle['model_year']) ?>" required>
        </div>
        <div class="mb-3"><label>Mileage (km)</label>
            <input type="number" name="mileage" class="form-control" value="<?= htmlspecialchars($vehicle['mileage']) ?>" required>
        </div>
        <div class="mb-3"><label>Fuel Type</label>
            <input type="text" name="fuel_type" class="form-control" value="<?= htmlspecialchars($vehicle['fuel_type']) ?>" required>
        </div>
        <div class="mb-3"><label>Transmission</label>
            <input type="text" name="transmission" class="form-control" value="<?= htmlspecialchars($vehicle['transmission']) ?>" required>
        </div>
        <div class="mb-3"><label>Condition</label>
            <input type="text" name="condition" class="form-control" value="<?= htmlspecialchars($vehicle['condition']) ?>" required>
        </div>
        <div class="mb-3"><label>Engine Capacity</label>
            <input type="text" name="engine_capacity" class="form-control" value="<?= htmlspecialchars($vehicle['engine_capacity']) ?>" required>
        </div>
        <div class="mb-3"><label>Color</label>
            <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($vehicle['color']) ?>" required>
        </div>
        <div class="mb-3"><label>Registration City</label>
            <input type="text" name="registration_city" class="form-control" value="<?= htmlspecialchars($vehicle['registration_city']) ?>" required>
        </div>
        <div class="mb-3"><label>Category</label>
            <select class="form-select" name="category" required>
                <option value="new" <?= ($vehicle['category'] == 'new') ? 'selected' : '' ?>>New</option>
                <option value="used" <?= ($vehicle['category'] == 'used') ? 'selected' : '' ?>>Used</option>
                <option value="ev" <?= ($vehicle['category'] == 'ev') ? 'selected' : '' ?>>EV</option>
            </select>
        </div>
        <div class="mb-3"><label>Price (PKR)</label>
            <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($vehicle['price']) ?>" required>
        </div>
        <div class="mb-3"><label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($vehicle['description']) ?></textarea>
        </div>
        <div class="mb-3"><label>Features</label>
            <textarea name="features" class="form-control" rows="3"><?= htmlspecialchars($vehicle['features']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Vehicle</button>
        <a href="your_vehicles.php" class="btn btn-secondary">Back</a>
    </form>

    </div>
    <?php
    include "footer.php";
    ?>
</body>
</html>