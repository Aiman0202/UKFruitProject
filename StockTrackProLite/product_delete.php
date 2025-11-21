<?php
include __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    // Set the active status to 0 for the specified product ID.
    $sql_hide = "UPDATE products SET is_active = 0 WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql_hide);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: products.php?status=product_hidden");
    exit();
}
