<?php
include __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
//mysql_query("DELETE FROM products WHERE id=$id");
if ($id) {
    // This is the entire 'deletion' process now.
    // It sets the active status to 0 for the specified product ID.
    $sql_hide = "UPDATE products SET is_active = 0 WHERE id = $id";
    
    // Using prepared statement for security (essential)
    $stmt = mysqli_prepare($conn, $sql_hide);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // No need to update or delete rows in deliveries, order_items, etc.
    // They still reference the product, but it's now 'hidden'.

    // Redirect the user back to the product list page
    header("Location: products_list.php?status=product_hidden");
    exit();
}
