<?php
include 'includes/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pidNew = (int)$_POST['product_id'];
    $qtyNew = (int)$_POST['qty'];
    $refNew = mysqli_real_escape_string($conn, $_POST['ref']);

    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_id, qty FROM deliveries WHERE id=$id"));

    mysqli_query($conn,
       "UPDATE deliveries
        SET product_id = $pidNew,
            qty        = $qtyNew,
            supplier_ref = '$refNew'
        WHERE id = $id
    ");

    $delta = $qtyNew - $old['qty'];

if ($pidNew == $old['product_id']) {
    mysqli_query($conn, 
       "UPDATE products
        SET stock = stock + $delta
        WHERE id = $pidNew
    ");
}
else {
    mysqli_query($conn, 
       "UPDATE products
        SET stock = stock - {$old['qty']}
        WHERE id = {$old['product_id']}
    ");
    mysqli_query($conn, 
       "UPDATE products
        SET stock = stock + $qtyNew
        WHERE id = $pidNew
    ");
}


    header('Location: deliveries.php?msg=updated');
    exit();
}

$row = mysqli_fetch_assoc(mysqli_query($conn, 
   "SELECT d.*, p.sku, p.name
    FROM deliveries d
    JOIN products p ON p.id = d.product_id
    WHERE d.id = $id
"));
if (!$row) {
    echo "<p class='notice'>Delivery not found.</p>";
    include 'includes/footer.php';
    exit();
}

$prods = mysqli_query($conn, "SELECT id, sku, name FROM products ORDER BY name");
?>
<h2>Edit Delivery #<?php echo $id; ?></h2>

<form action="delivery_edit.php?id=<?php echo $id; ?>" method="post">
    <label>Product
        <select name="product_id" required>
            <?php while ($p = mysqli_fetch_assoc($prods)): ?>
                <option value="<?php echo $p['id']; ?>"
                    <?php if ($p['id'] == $row['product_id']) echo 'selected'; ?>>
                    <?php echo $p['sku'].' - '.htmlspecialchars($p['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>Quantity
        <input type="number" name="qty" min="1"
               value="<?php echo $row['qty']; ?>" required>
    </label>

    <label>Supplier Ref
        <input type="text" name="ref"
               value="<?php echo htmlspecialchars($row['supplier_ref']); ?>">
    </label>

    <p>
        <input type="submit" value="Save Changes">
        <a href="deliveries.php" class="btn-secondary">Cancel</a>  <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include 'includes/footer.php'; ?>
