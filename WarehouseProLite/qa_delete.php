<?php
include 'includes/db.php';
include 'includes/auth.php'; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    mysqli_query($conn, "DELETE FROM qa_samples WHERE id = $id");
}

header('Location: qa_samples.php?msg=deleted');
exit();
?>
