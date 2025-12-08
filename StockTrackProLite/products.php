<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$res = mysqli_query($conn, "SELECT p.id, p.sku, p.name, p.price, p.stock,
           IFNULL(c.name,'-') AS category, p.country, p.class, p.best_before_days, p.pack_uom, p.default_pack_weight, p.lot_prefix
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.is_active = 1
    ORDER BY p.name
");

?>
<h2>Products</h2>

<p>
    <a href="add_product.php" class="btn">+ Add Product</a>
</p>

<table>
    <thead>
        <tr>
            <th>SKU</th><th>Name</th><th>Category</th>
            <th>Country</th><th>Class</th><th>Best Before Date</th>
            <th>Pack UOM</th><th>Default Pack Weight (g)</th><th>LOT Prefix</th>
            <th>Price (£)</th><th>Stock</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php if (mysqli_num_rows($res) === 0): ?>
        <tr><td colspan="6">No products yet.</td></tr>
<?php else: while ($row = mysqli_fetch_assoc($res)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['sku']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['country']); ?></td>
            <td><?php echo htmlspecialchars($row['class']); ?></td>
            <td><?php echo date('d-M-y', strtotime('+' . $row['best_before_days'] . ' days')); ?></td>
            <td><?php echo htmlspecialchars($row['pack_uom']); ?></td>
            <td><?php $weight = $row['default_pack_weight'] ?? NULL; 
                if (empty($weight) || $weight == 0) {
                    echo '-';
                } else {
                    echo number_format($weight, 0); 
                }
            ?></td>
            <td><?php echo htmlspecialchars($row['lot_prefix']); ?></td>
            <td><?php echo number_format($row['price'],2); ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td>
                <!-- NEW links -->
                <a href="product_edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="product_delete.php?id=<?php echo $row['id']; ?>"
                   onclick="return confirm('Delete this product?');">Delete</a>
            </td>
        </tr>
<?php endwhile; endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>
