<?php
/* adjustment_add.php – create +/- stock adjustment
   Optional query params:
   ?pid=11&delta=-20   → pre-fill product and qty_delta
*/
include 'includes/db.php';
include 'includes/header.php';

$takeId = isset($_GET['take']) ? (int)$_GET['take'] : 0;

/* --------- Save on POST --------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid   = (int)$_POST['product_id'];
    $delta = (int)$_POST['qty_delta'];
    $reason= mysqli_real_escape_string($conn, $_POST['reason']);

    /* approved_by could be the logged-in user’s ID; using 0 for legacy demo */
    $uid   = isset($_SESSION['wh_user_id']) ? (int)$_SESSION['wh_user_id'] : 'NULL';

    mysqli_query($conn, 
        "INSERT INTO adjustments (product_id, qty_delta, reason, approved_by, created_at)
        VALUES ($pid, $delta, '$reason', $uid, NOW())
    ");

    mysqli_query($conn,
    "UPDATE products 
     SET stock = stock + $delta 
     WHERE id = $pid"
);


$takeId = isset($_POST['take_id']) ? (int)$_POST['take_id'] : 0;

if ($takeId > 0) {
    header("Location: stocktake_view.php?id=$takeId&msg=adjustment_added");
} else {
    header("Location: adjustments.php?msg=added");
}
exit();
}

/* ---------- Load products for drop-down ---------- */
$prods = mysqli_query($conn, "SELECT id, sku, name FROM products ORDER BY name");

$prefillPid   = isset($_GET['pid'])   ? (int)$_GET['pid']   : '';
$prefillDelta = isset($_GET['delta']) ? (int)$_GET['delta'] : '';
?>
<h2>Add Adjustment</h2>

<form action="adjustment_add.php" method="post">

    <input type="hidden" name="take_id" value="<?php echo $takeId; ?>">
    <label>Product
        <select name="product_id" required>
            <option value="">-- select --</option>
            <?php while ($p = mysqli_fetch_assoc($prods)): ?>
                <option value="<?php echo $p['id']; ?>"
                    <?php if ($p['id'] == $prefillPid) echo 'selected'; ?>>
                    <?php echo $p['sku'].' - '.htmlspecialchars($p['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>Quantity Δ (e.g., -3 or 5)
        <input type="number" name="qty_delta"
               value="<?php echo $prefillDelta; ?>" required>
    </label>

    <label>Reason
        <select name="reason" required>
            <option value="damage">Damage</option>
            <option value="writeoff">Write-Off</option>
            <option value="correction" selected>Correction</option>
            <option value="qa_sample">QA Sample</option>   <!-- NEW -->
        </select>
    </label>


    <p>
        <input type="submit" value="Save">
        <a href="adjustments.php" class="btn-secondary">Cancel</a>  <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include 'includes/footer.php'; ?>
