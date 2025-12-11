 <link rel="stylesheet" href="../css/style.css">
<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_pass = md5($_POST['new_password']);

    $sql = "UPDATE users 
            SET password='$new_pass', must_reset_password='no'
            WHERE id='$user_id'";

    mysqli_query($conn, $sql);

    header('Location: dashboard.php');
    exit();
}
?>

<div class="forced-password-change">
    <form method="POST">

     <img src="assets/UKFruit2010.png" width="350" alt="Customer Logo" class="logo">
        <h2>You must change your password</h2>

        <input type="password"
            name="new_password"
            placeholder="Enter new password"
            required><br><br>

        <button type="submit">Update Password</button>
    </form>
</div>