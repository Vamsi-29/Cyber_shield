<?php
// ==== DATABASE CONFIGURATION ====

// Toggle this to switch between local and production
$local = true; // set false when deploying online

if ($local) {
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "cyber_shield"; // local DB
} else {
    $host = "sql300.infinityfree.com";      // production host
    $user = "if0_39876964";                 // production username
    $password = "Bruce1929";                // production password
    $dbname = "if0_39876964_users";         // production DB
}

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red;font-weight:bold;'>❌ Database Connection Failed: " . $conn->connect_error . "</p>");
}

// Optional: Set charset to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");
?>
