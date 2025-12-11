<?php
include 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_take'])) {

    $clerk = isset($_SESSION['wh_user_id']) ? (int)$_SESSION['wh_user_id'] : 'NULL';

    mysqli_query($conn, "INSERT INTO stock_takes (taken_at, clerk_id, note)
        VALUES (NOW(), $clerk, '')
    ");

    if (!mysqli_insert_id($conn)) {
        die('Insert failed: '.mysqli_error($conn));
    }

    $takeId = mysqli_insert_id($conn);

    header("Location: stocktake_new.php?take=$takeId");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_lines'])) {
    $takeId = (int)$_POST['take_id'];
    foreach ($_POST['count'] as $pid => $qty) {
        $pid  = (int)$pid;
        $qty  = (int)$qty;
        if ($qty === 0) continue;   
        mysqli_query($conn, "INSERT INTO stock_take_lines (stock_take_id, product_id, counted_qty)
            VALUES ($takeId, $pid, $qty)
            ON DUPLICATE KEY UPDATE counted_qty=$qty
        ");
    }
    header("Location: stocktake_view.php?id=$takeId");
    exit();
}

if (!isset($_GET['take'])):
?>
<h2>New Stock-Take</h2>
<form method="post">
    <p>This will create a new stock-take session for ALL products.</p>
    <input type="hidden" name="create_take" value="1">
    <input type="submit" value="Start Stock-Take"> 
    <a href="dashboard.php" class="btn-secondary">Cancel</a> <!--secondary 'cancel button' styling-->
</form>
<?php
include 'includes/footer.php';
exit();
endif;

$takeId = (int)$_GET['take'];
$prods  = mysqli_query($conn, "SELECT id, sku, name, stock FROM products ORDER BY name");
?>
<h2>Stock-Take #<?php echo $takeId; ?></h2>
<form method="post">
<input type="hidden" name="take_id" value="<?php echo $takeId; ?>">
<table>
<thead><tr><th>SKU</th><th>Name</th><th>Theoretical</th><th>Counted Qty</th></tr></thead>
<tbody>
<?php while ($p = mysqli_fetch_assoc($prods)): ?>
<tr>
    <td><?php echo $p['sku']; ?></td>
    <td><?php echo htmlspecialchars($p['name']); ?></td>
    <td><?php echo $p['stock']; ?></td>
    <td><input type="number" name="count[<?php echo $p['id']; ?>]"  class="stock-take-count"></td> <!--remove in-line css-->
</tr>
<?php endwhile; ?>
</tbody>
</table>
<p>
    <input type="hidden" name="save_lines" value="1">
    <input type="submit" value="Save Counts">
    <a href="stocktake_new.php" class="btn-secondary">Cancel</a> <!--secondary 'cancel button' styling-->
</p>
</form>
<?php include 'includes/footer.php'; ?>
