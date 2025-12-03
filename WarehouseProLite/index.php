<?php
session_start();
if (isset($_SESSION['wh_user'])) {
    header('Location: dashboard.php');
    exit();
}

/* simple flag for bad credentials */
$invalid = isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Warehouse Login</title>
    <link rel="stylesheet" href="../css/style.css"> <!--updated link to centralised css file-->
</head>
<body class="login-page">

<div class="login-card">
    <h1> <!--changed login card icons to match header icons-->
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M80-80v-481l280-119v80l200-80.67V-560h320v480H80Zm66.67-66.67h666.66V-493.2h-320V-582l-200 80v-78.67l-146.66 65v369Zm300-93.33h66.66v-160h-66.66v160Zm-160 0h66.66v-160h-66.66v160Zm320 0h66.66v-160h-66.66v160ZM880-560H693.33l40-320H840l40 320ZM146.67-146.67h666.66-666.66Z"/></svg>
         WarehouseProLite</h1>
         
    <p>Manage your inventory with ease.</p>
    <p><img src="assets/UKFruit2010.png" width="200" alt="Customer Logo" class="logo"></p>

    <?php if ($invalid): ?>
        <p class="password-invalid">Invalid username or password</p> <!--had in-line css-->
    <?php endif; ?>

    <form action="login.php" method="post">
        <label>Username
            <input type="text" name="username" required>
        </label>

        <label>Password
            <input type="password" name="password" required>
        </label>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>
