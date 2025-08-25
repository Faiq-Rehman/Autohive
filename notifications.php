<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark all as read when opening notifications page
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");

$sql = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2>My Notifications</h2>
    <ul class="list-group mt-3">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <li class="list-group-item">
                <?= htmlspecialchars($row['message']) ?><br>
                <small class="text-muted"><?= $row['created_at'] ?></small>
            </li>
        <?php } ?>
    </ul>
</body>
</html>