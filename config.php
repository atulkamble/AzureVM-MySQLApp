<?php
// Database configuration
$servername = "4.188.76.120";
$username = "admin";
$password = "admin";
$dbname = "student";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
