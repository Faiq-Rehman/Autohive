<?php
$host = "localhost";     // Database host (usually localhost)
$user = "root";          // Database username
$pass = "";              // Database password (empty for XAMPP default)
$dbname = "autohive"; // Replace with your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
