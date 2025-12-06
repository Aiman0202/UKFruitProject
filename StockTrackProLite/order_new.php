<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

/* ---------------- Handle POST save ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cust = (int)$_POST['customer_id'];
    $items = $_POST['item'];          // array [product_id => qty]

    /* calculate totals and build SQL values */
    $total = 0;
    $values = [];
    foreach ($items as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($qty <= 0) {
            continue;
        }

        /* get unit price */
        $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price, stock FROM products WHERE id=$pid"));
        $linePrice = $p['price'] * $qty;
        $values[] = "($pid, %ORDER_ID%, $qty, {$p['price']})";
        $total += $linePrice;

        /* reduce stock */
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id=$pid");
    }

    if ($cust && $total > 0) {
        /* create order header */
        mysqli_query($conn, "INSERT INTO orders (customer_id, order_date, total) VALUES ($cust, NOW(), $total)");
        $orderId = mysqli_insert_id($conn);

        /* insert order lines */
        foreach ($values as $v) {
            $v = str_replace('%ORDER_ID%', $orderId, $v);
            mysqli_query($conn, "INSERT INTO order_items (product_id, order_id, quantity, price) VALUES $v");
        }

        header("Location: order_view.php?id=$orderId");
        exit();
    }

    echo '<p class="notice">Something went wrong — check quantities.</p>';
}

/* ---------------- Load customers + products ---------------- */
$customers = mysqli_query($conn, "SELECT id, name FROM customers ORDER BY name");
$products  = mysqli_query($conn, "SELECT id, name, price, stock FROM products ORDER BY name");
?>
<h2>New Order</h2>

<form action="order_new.php" method="post">
    <label>Customer:
        <select name="customer_id" required>
            <option value="">-- select --</option>
            <?php while ($c = mysqli_fetch_assoc($customers)): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </label>

    <h3>Items</h3>
    <table>
        <thead><tr><th>Product</th><th>Stock</th><th>Price (£)</th><th>Qty</th></tr></thead>
        <tbody>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo $p['stock']; ?></td>
                <td><?php echo number_format($p['price'], 2); ?></td>
                <td>
                    <input class="input-field" type="number" name="item[<?php echo $p['id']; ?>]" value="0" min="0">
                    <!--removed style attribute from CSS-->
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <p>
        <input type="submit" value="Save Order">
        <a href="orders.php" class="btn-secondary">Cancel</a>  <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
