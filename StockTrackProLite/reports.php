<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

/* 1. Monthly sales (last 12 months) */
$monthly = mysqli_query($conn, "SELECT DATE_FORMAT(order_date,'%Y-%m') AS ym,
           COUNT(*) AS orders,
           SUM(total) AS revenue
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY ym
    ORDER BY ym
");

$monthlyData = mysqli_fetch_all($monthly, MYSQLI_ASSOC);

/* 2. Top 5 customers by spend (last 12 months) */
$topCust = mysqli_query($conn, "SELECT c.name,
           COUNT(o.id)  AS num_orders,
           SUM(o.total) AS spend
    FROM orders o
    JOIN customers c ON c.id = o.customer_id
    WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY c.id
    ORDER BY spend DESC
    LIMIT 5
");

$topCustomers = mysqli_fetch_all($topCust, MYSQLI_ASSOC);

/* 3. Low-stock products (< 20) */
$lowStock = mysqli_query($conn, "SELECT sku, name, stock
    FROM products
    WHERE stock < 20
    ORDER BY stock ASC
");

$lowStockItems = mysqli_fetch_all($lowStock, MYSQLI_ASSOC);

// labels and revenues for chart
$labels   = array_column($monthlyData, 'ym');
$revenues = array_map(fn($row) => round($row['revenue'], 2), $monthlyData);
?>

<h2>Reports</h2>

<h3>Monthly Sales (last 12 months)</h3>
<canvas id="salesChart" height="120"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx  = document.getElementById('salesChart').getContext('2d');

const data = {
    labels: <?php echo json_encode($labels); ?>,
    datasets: [{
        label: "Revenue £",
        data: <?php echo json_encode($revenues); ?>,
        backgroundColor: "rgba(46,139,87,0.5)",
        borderColor: "rgba(37,107,68,1)",
        borderWidth: 2
    }]
};

new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '£' + value
                }
            }
        }
    }
});
</script>

<h3>Top 5 Customers (last 12 months)</h3>
<table>
    <thead><tr><th>Customer</th><th>Orders</th><th>Spend (£)</th></tr></thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($topCust)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo $row['num_orders']; ?></td>
            <td><?php echo number_format($row['spend'], 2); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<h3>Low-stock Products (stock &lt; 20)</h3>
<table>
    <thead><tr><th>SKU</th><th>Name</th><th>Stock</th></tr></thead>
    <tbody>
    <?php if (mysqli_num_rows($lowStock) === 0): ?>
        <tr><td colspan="3">No items below threshold.</td></tr>
    <?php else: while ($row = mysqli_fetch_assoc($lowStock)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['sku']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo $row['stock']; ?></td>
        </tr>
    <?php endwhile; endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
