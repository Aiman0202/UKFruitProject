<?php
session_start();

include 'includes/db.php';
include 'includes/auth.php';   

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {

    $row = mysqli_fetch_assoc(mysqli_query($conn,
       "SELECT product_id, qty
        FROM deliveries
        WHERE id = $id
    "));

    if ($row) {
        mysqli_query($conn,
           "UPDATE products
            SET stock = stock - {$row['qty']}
            WHERE id = {$row['product_id']}
        ");

        mysqli_query($conn, "DELETE FROM deliveries WHERE id = $id");
    }
}

header('Location: deliveries.php?msg=deleted');
exit();
?>
