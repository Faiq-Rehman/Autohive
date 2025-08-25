<?php
session_start();
include '../dbconnect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function formatPKR($amount) {
    if ($amount >= 10000000) {
        return round($amount / 10000000, 1) . ' Crore';
    } elseif ($amount >= 100000) {
        return round($amount / 100000, 1) . ' Lac';
    } else {
        return number_format($amount);
    }
}


require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
require '../includes/fpdf/fpdf.php'; // ✅ PDF ke liye include

// ✅ Gmail Credentials (APP PASSWORD bina spaces ke)
$your_email = "team.autohive@gmail.com";
$app_password = "skxm awju ptvg ccws"; // ✅ Apna Gmail app password

// ✅ Transaction ID GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("❌ Invalid Transaction ID!");
}

// ✅ Fetch Transaction Details
$res = $conn->query("SELECT t.*, 
    b.name AS buyer_name, b.email AS buyer_email, b.phone AS buyer_phone,
    s.name AS seller_name, s.email AS seller_email, s.phone AS seller_phone,
    v.name AS car_name, v.brand, v.price
    FROM transactions t
    JOIN users b ON t.buyer_id=b.id
    JOIN users s ON t.seller_id=s.id
    JOIN vehicles v ON t.vehicle_id=v.id
    WHERE t.id=$id");

$t = $res->fetch_assoc();
if (!$t) {
    die("❌ Transaction not found!");
}

// ✅ PDF File Path
$pdf_file = "../receipts/receipt_$id.pdf";

// ✅ Generate PDF (if not exists)
if (!file_exists($pdf_file)) {
    if (!file_exists("../receipts")) {
        mkdir("../receipts", 0777, true);
    }

    $pdf = new FPDF();
    $pdf->AddPage();

    if (file_exists('../extraimages/logo.jpg')) {
        $pdf->Image('../extraimages/logo.jpg', 75, 10, 60, 30);
    }
    $pdf->Ln(35);

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
    $pdf->SetFillColor(240,240,240);
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
    $pdf->Cell(0, 8, "Car: " . $t['car_name'], 0, 1);
    $pdf->Cell(0, 8, "Brand: " . $t['brand'], 0, 1);
    $pdf->Cell(0, 8, "Model Year: " . $t['model_year'], 0, 1);
    $pdf->Cell(0, 8, "Mileage: " . $t['mileage'] . " KM", 0, 1);
    $pdf->Cell(0, 8, "Fuel Type: " . $t['fuel_type'], 0, 1);
    $pdf->Cell(0, 8, "Condition: " . $t['condition'], 0, 1);
    $pdf->Cell(0, 8, "Engine Capacity: " . $t['engine_capacity'], 0, 1);
    $pdf->Cell(0, 8, "Color: " . $t['color'], 0, 1);
    $pdf->Cell(0, 8, "Registration City: " . $t['registration_city'], 0, 1);
    $pdf->Cell(0, 8, "Category: " . ucfirst($t['category']), 0, 1);
    $pdf->Cell(0, 8, "Sell Type: " . ucfirst($t['sell_type']), 0, 1);
    $pdf->Cell(0, 8, "Price: PKR " . formatPKR($t['price']), 0, 1);
    $pdf->MultiCell(0, 8, "Description: " . $t['description'], 0, 1);
    $pdf->MultiCell(0, 8, "Features: " . $t['features'], 0, 1);
    $pdf->Cell(0,8,"Status: Completed",0,1);

    $pdf->Output('F', $pdf_file);
}

// ✅ Email Messages (SAME as you provided)
$buyer_message = "
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
";

$seller_message = "
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
";

// ✅ Send Email Function
function sendReceipt($to_email, $to_name, $pdf_file, $your_email, $app_password, $html_message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $your_email;
        $mail->Password = $app_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom($your_email, 'AutoHive');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = "AutoHive Transaction Receipt";
        $mail->Body = $html_message;
        $mail->addAttachment($pdf_file);

        $mail->send();
        echo "✅ Email Sent to $to_name ($to_email)<br>";
    } catch (Exception $e) {
        echo "❌ Mailer Error ($to_name): {$mail->ErrorInfo}<br>";
    }
}

// ✅ Send Emails Automatically
sendReceipt($t['buyer_email'], $t['buyer_name'], $pdf_file, $your_email, $app_password, $buyer_message);
sendReceipt($t['seller_email'], $t['seller_name'], $pdf_file, $your_email, $app_password, $seller_message);

echo "<br>✅ Receipt Sent Successfully to Buyer & Seller!";
?>
