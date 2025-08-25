<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['admin'])) {
    echo "<script>
        alert('You must be logged in as an admin to access this page.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

include '../dbconnect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// ✅ Debug Logs
function debug_log($msg) {
    echo "<pre style='color:red;'>[DEBUG] $msg</pre>";
}
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
require '../includes/fpdf/fpdf.php';

// ✅ Gmail Credentials
$your_email = "team.autohive@gmail.com";
$app_password = "skxm awju ptvg ccws"; // Gmail App Password

// ✅ Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// ✅ Delete vehicle if requested
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
    $del_stmt->bind_param("i", $delete_id);
    $del_stmt->execute();
    header("Location: manage_transaction.php"); // Refresh after delete
    exit();
}

// ✅ COMPLETE TRANSACTION + SEND EMAILS
if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);
    if ($id <= 0) {
        die("❌ Invalid Transaction ID!");
    }

    // ✅ Update Transaction (removed paperwork_done)
    $conn->query("UPDATE transactions SET status='completed', completed_at=NOW() WHERE id=$id");

    // Update Admin Vehicles (if transaction is linked to admin_vehicle_id)
    $conn->query("UPDATE vehicles v 
                  JOIN transactions t ON v.id=t.admin_vehicle_id 
                  SET v.status='sold' 
                  WHERE t.id=$id");

    // Update User Vehicles (if transaction is linked to user_vehicle_id)
    $conn->query("UPDATE user_vehicles uv 
                  JOIN transactions t ON uv.id=t.user_vehicle_id 
                  SET uv.status='sold' 
                  WHERE t.id=$id");

    // ✅ Fetch Transaction Details (covering both tables)
$stmt = $conn->prepare("
SELECT t.*, 
    b.name AS buyer_name, b.email AS buyer_email, b.phone AS buyer_phone,
    s.name AS seller_name, s.email AS seller_email, s.phone AS seller_phone,
    COALESCE(v.name, uv.name) AS car_name,
    COALESCE(v.brand, uv.brand) AS brand,
    COALESCE(v.price, uv.price) AS price,
    COALESCE(v.model_year, uv.model_year) AS model_year,
    COALESCE(v.mileage, uv.mileage) AS mileage,
    COALESCE(v.fuel_type, uv.fuel_type) AS fuel_type,
    COALESCE(v.transmission, uv.transmission) AS transmission,
    COALESCE(v.`condition`, uv.`condition`) AS `condition`,
    COALESCE(v.engine_capacity, uv.engine_capacity) AS engine_capacity,
    COALESCE(v.color, uv.color) AS color,
    COALESCE(v.registration_city, uv.registration_city) AS registration_city,
    COALESCE(v.category, uv.category) AS category,
    COALESCE(v.sell_type, uv.sell_type) AS sell_type,
    COALESCE(v.description, uv.description) AS description,
    COALESCE(v.features, uv.features) AS features
FROM transactions t
JOIN users b ON t.buyer_id = b.id
JOIN users s ON t.seller_id = s.id
LEFT JOIN vehicles v ON t.admin_vehicle_id = v.id
LEFT JOIN user_vehicles uv ON t.user_vehicle_id = uv.id
WHERE t.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$t = $res->fetch_assoc();

if (!$t) {
    die("❌ Transaction not found!");
}

    // ✅ Generate PDF Receipt
$pdf_file = "../receipts/receipt_$id.pdf";
if (!file_exists("../receipts")) {
    mkdir("../receipts", 0755, true);
}

$pdf = new FPDF();
$pdf->AddPage();

// Logo
if (file_exists('../extraimages/logo2.png')) {
    $pdf->Image('../extraimages/logo2.png', 75, 10, 60, 30);
}
$pdf->Ln(35);

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'AutoHive Transaction Receipt',0,1,'C');
$pdf->Ln(5);

// Buyer Details
$pdf->SetFillColor(240,240,240);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Buyer Details',0,1,'L',true);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Name: ".$t['buyer_name'],0,1);
$pdf->Cell(0,8,"Email: ".$t['buyer_email'],0,1);
$pdf->Cell(0,8,"Phone: ".$t['buyer_phone'],0,1);
$pdf->Ln(5);

// Seller Details
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Seller Details',0,1,'L',true);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Name: ".$t['seller_name'],0,1);
$pdf->Cell(0,8,"Email: ".$t['seller_email'],0,1);
$pdf->Cell(0,8,"Phone: ".$t['seller_phone'],0,1);
$pdf->Ln(5);

// Vehicle Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'Vehicle Details', 0, 1, 'L', true);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Car: " . ($t['car_name'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Brand: " . ($t['brand'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Model Year: " . ($t['model_year'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Mileage: " . (isset($t['mileage']) ? $t['mileage']." KM" : 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Fuel Type: " . ($t['fuel_type'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Condition: " . ($t['condition'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Engine Capacity: " . ($t['engine_capacity'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Color: " . ($t['color'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Registration City: " . ($t['registration_city'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Category: " . ucfirst($t['category'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Sell Type: " . ucfirst($t['sell_type'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 8, "Price: PKR " . formatPKR($t['price'] ?? 0), 0, 1);
$pdf->MultiCell(0, 8, "Description: " . ($t['description'] ?? 'N/A'), 0, 1);
$pdf->MultiCell(0, 8, "Features: " . ($t['features'] ?? 'N/A'), 0, 1);
$pdf->Cell(0,8,"Status: Completed",0,1);

// Save PDF
$pdf->Output('F', $pdf_file);

    // ✅ Prepare Email Recipients
    $emails = [
        ['email' => $t['buyer_email'], 'name' => $t['buyer_name']],
        ['email' => $t['seller_email'], 'name' => $t['seller_name']]
    ];

    // ✅ Send Emails
    $mail = new PHPMailer(true);
    foreach ($emails as $e) {
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $your_email;
            $mail->Password = $app_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($your_email, 'AutoHive');
            $mail->addAddress($e['email'], $e['name']);

            $mail->isHTML(true);
            $mail->Subject = "AutoHive Transaction Receipt";

            // ✅ Different message for Buyer and Seller
            if ($e['email'] == $t['buyer_email']) {
                $mail->Body = "
                <div style='color:black;'>
                Dear {$t['buyer_name']},<br><br>
                I hope you are doing well.<br><br>
                On behalf of the <b>AutoHive team</b>, I would like to sincerely thank you for choosing AutoHive to buy your vehicle. We are truly grateful for the trust you have placed in us.<br><br>
                Your purchase means a lot to us, and we are committed to providing you with the best experience possible. We hope your new vehicle brings you comfort, style, and satisfaction on every journey.<br><br>
                If you have any questions or need assistance regarding your purchase, please don’t hesitate to contact us.<br><br>
                Once again, thank you for buying from AutoHive. We look forward to serving you again in the future!<br><br>
                Best Regards,<br>
                <b>AutoHive Team</b><br>
                AutoHive – Drive the Future
                </div>";
            } else {
                $mail->Body = "
                <div style='color:black;'>
                Dear {$t['seller_name']},<br><br>
                I hope this message finds you well.<br><br>
                On behalf of the <b>AutoHive team</b>, I would like to express my sincere gratitude for choosing AutoHive to sell your product. We truly value the trust you have placed in our platform.<br><br>
                Your contribution helps us grow and provide quality products to our customers, and we are extremely thankful for your partnership. It’s a pleasure to have you as part of the AutoHive community, and we look forward to seeing more of your great listings in the future.<br><br>
                If you ever need assistance or have suggestions for improving our services, please feel free to reach out to us.<br><br>
                Once again, thank you for selling on AutoHive. We are grateful for your support and wish you great success in your business journey!<br><br>
                Best Regards,<br>
                <b>AutoHive Team</b><br>
                AutoHive – Drive the Future
                </div>";
            }
            $mail->addAttachment($pdf_file);
            $mail->send();
        } catch (Exception $ex) {
            echo "❌ Email to {$e['name']} failed: " . $mail->ErrorInfo . "<br>";
        }
        $mail->clearAddresses();
        $mail->clearAttachments();
    }

    echo "<script>alert('✅ Transaction completed & emails sent successfully!');</script>";
    exit;
}

// ✅ Fetch Transactions List (covering both sources)
$transactions = $conn->query("SELECT t.*, b.name AS buyer, s.name AS seller, 
    COALESCE(v.name, uv.name) AS car_name, 
    COALESCE(v.price, uv.price) AS price
    FROM transactions t
    JOIN users b ON t.buyer_id=b.id
    JOIN users s ON t.seller_id=s.id
    LEFT JOIN vehicles v ON t.admin_vehicle_id=v.id
    LEFT JOIN user_vehicles uv ON t.user_vehicle_id=uv.id
    ORDER BY t.id DESC");

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
  <title>Transaction Receipts - AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/manage_transaction.css">
</head>
<body>

<!-- ✅ SIDEBAR -->
<div class="sidebar">
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
  <div class="logout-container">
    <a href="?logout=true" class="logout-btn">Logout</a>
  </div>
</div>

<!-- ✅ MAIN CONTENT -->
<div class="main-content">

<div class="d-flex justify-content-between align-items-center mb-4">
  <h3 class="fw-bold text-center mb-4">Manage Transaction Receipts</h3>
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
        <th>Car</th>
        <th>Buyer</th>
        <th>Seller</th>
        <th>Price</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($t = $transactions->fetch_assoc()) { ?>
      <tr>
        <td><?php echo $t['id']; ?></td>
        <td><?php echo $t['car_name']; ?></td>
        <td><?php echo $t['buyer']; ?></td>
        <td><?php echo $t['seller']; ?></td>
        <td><?php echo formatPKR($t['price']); ?></td>
        <td><?php echo ucfirst($t['status']); ?></td>
        <td class="text-center">
          <?php if($t['status']=='pending') { ?>
            <a href="manage_transaction.php?complete=<?php echo $t['id']; ?>" 
               class="btn btn-outline-success btn-sm"
               onclick="return confirm('Mark this transaction as completed and send emails?');">
              ✅ Complete Paperwork
            </a>
          <?php } else { ?>
            <a href="../receipts/receipt_<?php echo $t['id']; ?>.pdf" 
               class="btn btn-outline-primary btn-sm" target="_blank">
              📄 Download Receipt
            </a>
          <?php } ?>

          <a href="?delete=<?php echo $t['id']; ?>" 
             class="btn btn-outline-danger btn-sm"
             onclick="return confirm('Are you sure you want to delete this transaction?')">
            🗑 Delete
          </a>
          
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-outline-dark">Back</a>
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
