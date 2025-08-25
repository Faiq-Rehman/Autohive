<?php
session_start();
include '../dbconnect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("❌ Invalid or missing transaction ID!");
}

$pdf_file = "../receipts/receipt_$id.pdf";

if (!file_exists($pdf_file)) {
    die("❌ Receipt not found for Transaction ID: $id");
}

// ✅ Show PDF in browser
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="receipt_'.$id.'.pdf"');
readfile($pdf_file);
exit;
?>