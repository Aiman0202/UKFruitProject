<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

/* ---------- Handle INSERT ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku   = mysqli_real_escape_string($conn, $_POST['sku']);
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $cat   = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    mysqli_query($conn,
       "INSERT INTO products (sku, name, category_id, price, stock)
        VALUES (
            '$sku',
            '$name',
            ".($cat ?: 'NULL').",
            $price,
            $stock
        )
    ");

    header('Location: products.php?msg=added');
    exit();
}

/* ------- Load categories for drop-down ------- */
$cats = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
?>
<h2>Add Product</h2>

<form action="add_product.php" method="post">
    <label>SKU
        <input type="text" name="sku" required>
    </label>

    <label>Name
        <input type="text" name="name" required>
    </label>

    <label>Category
        <select name="category_id">
            <option value="">- none -</option>
            <?php while ($c = mysqli_fetch_assoc($cats)): ?>
                <option value="<?php echo $c['id']; ?>">
                    <?php echo htmlspecialchars($c['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>Price (£)
        <input type="number" step="0.01" name="price" required>
    </label>

    <label>Stock
        <input type="number" name="stock" value="0" required>
    </label>

    <p>
        <input type="submit" value="Add Product">
        <a href="products.php">Cancel</a>
    </p>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
