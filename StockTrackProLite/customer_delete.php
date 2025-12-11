<?php
include __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    $sql_hide = "UPDATE customers SET is_active = 0 WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql_hide);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: customers.php?status=customer_hidden");
    exit();
}
