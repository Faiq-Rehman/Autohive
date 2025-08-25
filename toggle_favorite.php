<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$vehicle_id = intval($_GET['id']);

// Check if already in favorites
$check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND vehicle_id = ?");
$check->bind_param("ii", $user_id, $vehicle_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // Remove from favorites
    $del = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND vehicle_id = ?");
    $del->bind_param("ii", $user_id, $vehicle_id);
    $del->execute();
} else {
    // Add to favorites
    $ins = $conn->prepare("INSERT INTO favorites (user_id, vehicle_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $vehicle_id);
    $ins->execute();
}

header("Location: view_vehicle.php?id=" . $vehicle_id);
exit();
?>