<?php
include 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];
    $ref = mysqli_real_escape_string($conn, $_POST['ref']);

    mysqli_query($conn,
       "INSERT INTO deliveries (product_id, qty, received_at, supplier_ref)
        VALUES ($pid, $qty, NOW(), '$ref')
    ");

    mysqli_query($conn, 
       "UPDATE products
        SET stock = stock + $qty
        WHERE id = $pid
    ");

    header('Location: deliveries.php?msg=added');
    exit();
}

$prods = mysqli_query($conn, "SELECT id, sku, name FROM products ORDER BY name");
?>
<h2>Record Delivery</h2>

<form action="delivery_add.php" method="post">
    <label>Product
        <select name="product_id" required>
            <option value="">-- select --</option>
            <?php while ($p = mysqli_fetch_assoc($prods)): ?>
                <option value="<?php echo $p['id']; ?>">
                    <?php echo $p['sku'].' - '.htmlspecialchars($p['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>Quantity
        <input type="number" name="qty" min="1" required>
    </label>

    <label>Supplier Ref
        <input type="text" name="ref">
    </label>

    <p>
        <input type="submit" value="Save Delivery">
        <a href="deliveries.php"  class="btn-secondary">Cancel</a> <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include 'includes/footer.php'; ?>
