<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku   = mysqli_real_escape_string($conn, $_POST['sku']);
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $cat   = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $pack_uom = mysqli_real_escape_string($conn, $_POST['pack_uom']);
    $default_pack_weight = !empty($_POST['default_pack_weight']) ? (int)$_POST['default_pack_weight'] : 'NULL';
    $best_before_days = (int)$_POST['best_before_days'];
    $lot_prefix = mysqli_real_escape_string($conn, $_POST['lot_prefix']);

    mysqli_query($conn,
       "INSERT INTO products (sku, name, category_id, price, stock, country, class, pack_uom, default_pack_weight, best_before_days, lot_prefix)
        VALUES (
            '$sku',
            '$name',
            ".($cat ?: 'NULL').",
            $price,
            $stock,
            '$country',
            '$class',
            '$pack_uom',
            $default_pack_weight,
            $best_before_days,
            '$lot_prefix'
        )
    ");

    header('Location: products.php?msg=added');
    exit();
}

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

    <label>Country
        <input type="text" name="country" required>
    </label>

    <label>Class
        <input type="text" name="class" required maxlength="10" placeholder="e.g., I, II, III">
    </label>

    <label>Pack UOM
        <select name="pack_uom" required>
            <option value="">- none -</option>
            <option value="each">each</option>
            <option value="g">g (grams)</option>
        </select>
    </label>

    <label>Default Pack Weight (g)
        <input type="number" name="default_pack_weight" placeholder="Only for packaged items">
    </label>

    <label>Best Before Days
        <input type="number" name="best_before_days" required>
    </label>

    <label>Lot Prefix
        <input type="text" name="lot_prefix" required maxlength="2">
    </label>

    <label>Price (£)
        <input type="number" step="0.01" name="price" required>
    </label>

    <label>Stock
        <input type="number" name="stock" value="0" required>
    </label>

    <p>
        <input type="submit" value="Add Product"> 
        <a href="products.php" class="btn-secondary">Cancel</a>  <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
