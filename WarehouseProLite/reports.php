<?php
include 'includes/db.php';
include 'includes/header.php';

$delivToday = mysqli_query($conn, "SELECT d.id,
           d.received_at,
           p.sku,
           p.name,
           d.qty,
           d.supplier_ref
    FROM deliveries d
    JOIN products p ON p.id = d.product_id
    WHERE DATE(d.received_at) = CURDATE()
    ORDER BY d.received_at DESC
");

$adj30 = mysqli_query($conn, "SELECT a.id,
           a.created_at,
           p.sku,
           p.name,
           a.qty_delta,
           a.reason
    FROM adjustments a
    JOIN products p ON p.id = a.product_id
    WHERE a.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY a.created_at DESC
");

$qaFail = mysqli_query($conn, "SELECT q.id,
           q.sample_time,
           p.sku,
           p.name,
           q.brix,
           q.temperature
    FROM qa_samples q
    JOIN products  p ON p.id = q.product_id
    WHERE q.passed = 'no'
      AND q.sample_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY q.sample_time DESC
");
?>
<h2>Warehouse Reports</h2>

<h3>Deliveries Today (<?php echo date('Y-m-d'); ?>)</h3>
<table>
  <thead><tr><th>ID</th><th>Time</th><th>SKU</th><th>Name</th><th>Qty</th><th>Supplier Ref</th></tr></thead>
  <tbody>
  <?php if (mysqli_num_rows($delivToday)==0): ?>
    <tr><td colspan="6">No deliveries recorded today.</td></tr>
  <?php else: while ($r=mysqli_fetch_assoc($delivToday)): ?>
    <tr>
      <td><?php echo $r['id']; ?></td>
      <td><?php echo date('H:i',strtotime($r['received_at'])); ?></td>
      <td><?php echo $r['sku']; ?></td>
      <td><?php echo htmlspecialchars($r['name']); ?></td>
      <td><?php echo $r['qty']; ?></td>
      <td><?php echo htmlspecialchars($r['supplier_ref']); ?></td>
    </tr>
  <?php endwhile; endif; ?>
  </tbody>
</table>

<h3>Adjustments (last 30 days)</h3>
<table>
  <thead><tr><th>ID</th><th>Date</th><th>SKU</th><th>Name</th><th>Δ Qty</th><th>Reason</th></tr></thead>
  <tbody>
  <?php if (mysqli_num_rows($adj30)==0): ?>
    <tr><td colspan="6">No adjustments in last 30 days.</td></tr>
  <?php else: while ($r=mysqli_fetch_assoc($adj30)): ?>
    <tr>
      <td><?php echo $r['id']; ?></td>
      <td><?php echo date('Y-m-d',strtotime($r['created_at'])); ?></td>
      <td><?php echo $r['sku']; ?></td>
      <td><?php echo htmlspecialchars($r['name']); ?></td>
      <td><?php echo $r['qty_delta']; ?></td>
      <td><?php echo $r['reason']; ?></td>
    </tr>
  <?php endwhile; endif; ?>
  </tbody>
</table>

<h3>QA Failures (last 30 days)</h3>
<table>
  <thead><tr><th>ID</th><th>Date</th><th>SKU</th><th>Name</th><th>Brix</th><th>Temp °C</th></tr></thead>
  <tbody>
  <?php if (mysqli_num_rows($qaFail)==0): ?>
    <tr><td colspan="6">No failed QA samples in last 30 days.</td></tr>
  <?php else: while ($r=mysqli_fetch_assoc($qaFail)): ?>
    <tr>
      <td><?php echo $r['id']; ?></td>
      <td><?php echo date('Y-m-d',strtotime($r['sample_time'])); ?></td>
      <td><?php echo $r['sku']; ?></td>
      <td><?php echo htmlspecialchars($r['name']); ?></td>
      <td><?php echo $r['brix']; ?></td>
      <td><?php echo $r['temperature']; ?></td>
    </tr>
  <?php endwhile; endif; ?>
  </tbody>
</table>

<p><a href="dashboard.php"  class="btn">← Back to Dashboard</a></p> <!--turned link to button-->

<?php include 'includes/footer.php'; ?>
