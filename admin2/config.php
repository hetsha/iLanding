<?php
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$database = "pro";

$conn = new mysqli($servername, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

