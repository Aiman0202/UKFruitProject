<?php
/* delivery_delete.php – remove a goods-in record + reverse stock */
include 'includes/db.php';
include 'includes/auth.php';   // ensure user is logged in

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {

    /* 1. Grab product + qty before deleting */
    $row = mysqli_fetch_assoc(mysqli_query($conn,
       "SELECT product_id, qty
        FROM deliveries
        WHERE id = $id
    "));

    if ($row) {
        /* 2. Subtract quantity from product stock */
        mysqli_query($conn,
           "UPDATE products
            SET stock = stock - {$row['qty']}
            WHERE id = {$row['product_id']}
        ");

        /* 3. Now delete the delivery row */
        mysqli_query($conn, "DELETE FROM deliveries WHERE id = $id");
    }
}

header('Location: deliveries.php?msg=deleted');
exit();
?>
