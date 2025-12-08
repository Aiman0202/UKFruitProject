<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ---------- save ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku  = mysqli_real_escape_string($conn, $_POST['sku']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat  = (int)$_POST['category_id'];
    $price= (float)$_POST['price'];
    $stock= (int)$_POST['stock'];
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $pack_uom = mysqli_real_escape_string($conn, $_POST['pack_uom']);
    $default_pack_weight = !empty($_POST['default_pack_weight']) ? (int)$_POST['default_pack_weight'] : 'NULL';
    $best_before_days = (int)$_POST['best_before_days'];
    $lot_prefix = mysqli_real_escape_string($conn, $_POST['lot_prefix']);

    mysqli_query($conn, 
           "UPDATE products SET
            sku='$sku', name='$name',
            category_id=".($cat?:'NULL').",
            price=$price, stock=$stock,
            country='$country',
            class='$class',
            pack_uom='$pack_uom',
            default_pack_weight=$default_pack_weight,
            best_before_days=$best_before_days,
            lot_prefix='$lot_prefix'
        WHERE id=$id
    ");
    header('Location: products.php?msg=updated');
    exit();
}

/* load row */
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
if (!$row) { echo "<p class='notice'>Product not found.</p>"; include __DIR__ . '/includes/footer.php'; exit; }

/* categories for dropdown */
$cats = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
?>
<h2>Edit Product</h2>

<form action="product_edit.php?id=<?php echo $id; ?>" method="post">
    <label>SKU
        <input type="text" name="sku" value="<?php echo htmlspecialchars($row['sku']); ?>" required>
    </label>

    <label>Name
        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
    </label>

    <label>Category
        <select name="category_id">
            <option value="">- none -</option>
            <?php while ($c = mysqli_fetch_assoc($cats)): ?>
                <option value="<?php echo $c['id']; ?>"
                    <?php if ($c['id']==$row['category_id']) {
    echo 'selected';
} ?>>
                    <?php echo htmlspecialchars($c['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>Country
        <input type= "text" name="country" value="<?php echo htmlspecialchars($row['country']); ?>" required>
    </label>

    <label>Class
        <input type= "text" name="class" value="<?php echo htmlspecialchars($row['class']); ?>" required>
    </label>

    <label>Pack UOM
        <select name="pack_uom" required>
            <option value="">- none -</option>
            <option value="each" <?php if ($row['pack_uom'] == 'each') echo 'selected'; ?>>each</option>
            <option value="g" <?php if ($row['pack_uom'] == 'g') echo 'selected'; ?>>g (grams)</option>
        </select>
    </label>

    <label>Default Pack Weight (g)
        <input type="number" step="1" name="default_pack_weight" 
               value="<?php echo !empty($row['default_pack_weight']) ? (int)$row['default_pack_weight'] : ''; ?>"
               min="0" placeholder="Only for packaged items">
    </label>

    <label>Best Before Days
        <input type="number" name="best_before_days" 
               value="<?php echo $row['best_before_days']; ?>" required min="0">
    </label>

    <label>Lot Prefix
        <input type="text" name="lot_prefix" value="<?php echo htmlspecialchars($row['lot_prefix']); ?>" required maxlength="10">
    </label>

    <label>Price (£)
        <input type="number" step="0.01" name="price"
               value="<?php echo number_format($row['price'],2,'.',''); ?>" required>
    </label>

    <label>Stock
        <input type="number" name="stock" value="<?php echo $row['stock']; ?>" required>
    </label>

    <p>
        <input type="submit" value="Save">
        <a href="products.php">Cancel</a>
    </p>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
