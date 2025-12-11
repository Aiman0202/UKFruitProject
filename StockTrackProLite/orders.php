<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$orders = mysqli_fetch_all(mysqli_query($conn, "SELECT o.id, o.order_date, o.total,
           c.name AS customer
    FROM orders o
    LEFT JOIN customers c ON c.id = o.customer_id
    ORDER BY o.order_date DESC
"), MYSQLI_ASSOC);

?>
<h2>Orders</h2>

<!--style="display:flex; align-items:center; gap: 550px;0px; margin-bottom:12px;"-->
<!--        style="padding:6px; width:250px; margin-bottom:12px;"-->
<div class="orders-button-input">
    <a href="order_new.php" class="btn">+ New Order</a>

    <input 
        type="text" 
        id="searchCustomer" 
        placeholder="Search customer name..." 
    >

</div>



<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Total (£)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($orders)): ?>
        <tr><td colspan="5">No orders yet.</td></tr>
    <?php else: ?>
        <?php foreach ($orders as $row): ?>
            <tr data-customer="<?php echo strtolower($row['customer'] ?? ''); ?>">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($row['order_date'])); ?></td>
                <td><?php echo htmlspecialchars($row['customer'] ?? '-'); ?></td>
                <td><?php echo number_format($row['total'], 2); ?></td>
                <td><a href="order_view.php?id=<?php echo $row['id']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<script>
document.getElementById("searchCustomer").addEventListener("keyup", function() {
    const query = this.value.toLowerCase();
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const customer = row.getAttribute("data-customer");

        if (customer.includes(query)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>