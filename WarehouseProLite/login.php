<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = md5($_POST['password']);

    $res = mysqli_query($conn, "SELECT id, username, role
        FROM wh_users
        WHERE username='$u' AND password='$p'
        LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($res)) {

        /* valid login — store details */
        $_SESSION['wh_user_id'] = $row['id'];
        $_SESSION['wh_user']    = $row['username'];
        $_SESSION['wh_role']    = $row['role'];

        header('Location: dashboard.php');
        exit();
    }
}

/* failed login — back to index with error flag */
header('Location: index.php?error=1');
exit();
?>
