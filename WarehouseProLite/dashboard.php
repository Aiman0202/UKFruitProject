<?php
/* dashboard.php – WarehouseProLite overview */
include 'includes/db.php';
include 'includes/header.php';

$totalDeliveries = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM deliveries"))['c'];

$todayDeliveries = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM deliveries
                 WHERE DATE(received_at) = CURDATE()"))['c'];

$recentAdjust = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM adjustments
                 WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"))['c'];

$recentQAFails = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM qa_samples
                 WHERE passed = 'no'
                 AND sample_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"))['c'];
?>
<h2>Dashboard</h2>

<div class="cards">
  <div class="card"><span class="big"><?php echo $totalDeliveries; ?></span>Total&nbsp;Deliveries</div>
  <div class="card"><span class="big"><?php echo $todayDeliveries; ?></span>Today</div>
  <div class="card"><span class="big"><?php echo $recentAdjust; ?></span>Adj&nbsp;(30d)</div>
  <div class="card"><span class="big"><?php echo $recentQAFails; ?></span>QA&nbsp;Fails&nbsp;(30d)</div>
</div>
<br>

<h3>Quick Links</h3>
<div class="quick-links">
  <a href="deliveries.php" class="btn">Record Deliveries</a>
  <a href="stocktake_new.php" class="btn">Start Stock-Take</a>
  <a href="adjustments.php" class="btn">Add Adjustment</a>
  <a href="qa_samples.php" class="btn">Log QA Sample</a>
  <a href="reports.php" class="btn">Warehouse Reports</a>
</div>

<?php include 'includes/footer.php'; ?>
