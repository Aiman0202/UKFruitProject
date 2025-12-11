<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WarehouseProLite</title>

    <!-- link to centralised stylesheet -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header class='topbar'> <!--updated icon-->
  <h1 class='logo'>
    <nav>
        <a href="dashboard.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF"><path d="M80-80v-481l280-119v80l200-80v120h320v480H80Zm80-80h640v-320H480v-82l-200 80v-78l-120 53v347Zm280-80h80v-160h-80v160Zm-160 0h80v-160h-80v160Zm320 0h80v-160h-80v160Zm280-320H680l40-320h120l40 320ZM160-160h640-640Z"/></svg></a>
        <a href="dashboard.php"> Warehouse Pro Lite </a>
    </nav>
  </h1>

  <nav>
    <a href='dashboard.php'   class='<?php echo basename($_SERVER['PHP_SELF'])=='dashboard.php'   ? 'active' :'';?>'>Dashboard</a>
    <a href='deliveries.php'  class='<?php echo basename($_SERVER['PHP_SELF'])=='deliveries.php'  ? 'active' :'';?>'>Deliveries</a>
    <a href='stocktake_new.php' class='<?php echo basename($_SERVER['PHP_SELF'])=='stocktake_new.php' ? 'active' :'';?>'>Stock-Take</a>
    <a href='stocktakes.php' class='<?php echo basename($_SERVER["PHP_SELF"])=="stocktakes.php"?"active":"";?>'>Take History</a>
    <a href='adjustments.php' class='<?php echo basename($_SERVER['PHP_SELF'])=='adjustments.php' ? 'active' :'';?>'>Adjustments</a>
    <a href='qa_samples.php'  class='<?php echo basename($_SERVER['PHP_SELF'])=='qa_samples.php'  ? 'active' :'';?>'>QA Samples</a>
    <a href='reports.php'     class='<?php echo basename($_SERVER['PHP_SELF'])=='reports.php'     ? 'active' :'';?>'>Reports</a>
    <a href='logout.php' class='right'>Logout</a>
  </nav>
</header>
<main class='container'>
