<?php
include __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
mysqli_query($conn, "DELETE FROM products WHERE id=$id");
header('Location: products.php?msg=deleted');
exit();
