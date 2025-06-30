<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notes_sharing";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Optional: Set character set to utf8mb4 for better emoji/special character support
$conn->set_charset("utf8mb4");
