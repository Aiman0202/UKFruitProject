<?php
// login.php  (place in StockTrackProLite root)
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = md5($_POST['password']);

    $res = mysqli_query($conn, "SELECT id, username, role
        FROM users
        WHERE username='$u' AND password='$p'
        LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($res)) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user']    = $row['username'];
        $_SESSION['role']    = $row['role'];

        if ($row['must_reset_password'] === 'yes') {
            header('Location: force_password_change.php');
            exit();
        }

        header('Location: dashboard.php');
        exit();
    }
}

header('Location: index.php?error=1');
exit();
?>
