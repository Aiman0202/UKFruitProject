<?php
/* dashboard.php – legacy “sexy” summary */
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

/* --- Quick KPIs --- */
$totCust = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM customers"))['total'];
$totProd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$totOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];


/* low-stock threshold */
$lowCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock < 20"))['total'];


/* orders this month */
$monthOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total
    FROM orders
    WHERE YEAR(order_date) = YEAR(CURDATE())
      AND MONTH(order_date) = MONTH(CURDATE())
"))['total'];


/* most recent 5 orders */
$recent = mysqli_query($conn, "SELECT o.id, o.order_date, o.total, c.name AS customer
    FROM orders o
    LEFT JOIN customers c ON c.id = o.customer_id
    ORDER BY o.order_date DESC
    LIMIT 5
");
?>
<h2>Dashboard</h2>

<!--adding linkd to dashboard key performance indicators-->
<div class="cards">
    <a href="customers.php" class="card kpi"><span class="big"><?php echo $totCust; ?></span>Customers</a>
    <a href="products.php" class="card kpi"><span class="big"><?php echo $totProd; ?></span>Products</a>
    <a href="products.php" class="card kpi"><span class="big"><?php echo $lowCount; ?></span>Low-stock&nbsp;items</a>
    <a href="orders.php"  class="card kpi"><span class="big"><?php echo $totOrders; ?></span>Total&nbsp;orders</a>
    <a href="orders.php" class="card kpi"><span class="big"><?php echo $monthOrders; ?></span>Orders&nbsp;this&nbsp;month</a>
</div>

<h3>Recent Orders</h3>
<table>
    <thead><tr><th>#</th><th>Date</th><th>Customer</th><th>Total (£)</th></tr></thead>
    <tbody>
<?php while ($row = mysqli_fetch_assoc($recent)): ?>
        <tr>
            <td><a href="order_view.php?id=<?php echo $row['id']; ?>">
                <?php echo $row['id']; ?></a></td>
            <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
            <td><?php echo htmlspecialchars($row['customer']); ?></td>
            <td><?php echo number_format($row['total'], 2); ?></td>
        </tr>
<?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
