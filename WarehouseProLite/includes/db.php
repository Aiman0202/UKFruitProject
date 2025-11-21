<?php
// Connect to MySQL using mysqli
$conn = mysqli_connect("127.0.0.1", "root", "", "stocktrackpro", 3306);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>
