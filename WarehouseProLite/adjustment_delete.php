<?php
include 'includes/db.php';
include 'includes/auth.php';     

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    mysqli_query($conn, "DELETE FROM adjustments WHERE id=$id");
}
header('Location: adjustments.php?msg=deleted');
exit();
