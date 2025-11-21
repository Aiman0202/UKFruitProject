<?php
/* customer_delete.php – Simple delete + redirect */
include __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
header('Location: customers.php?msg=deleted');
exit();
