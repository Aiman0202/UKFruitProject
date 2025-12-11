<?php
/**
 * This file handles both:
 * - UI mode: Shows product selection page
 * - Print mode: Generates HTML labels
 */

session_start();
include __DIR__ . '/includes/db.php';

// Check database connection
if (!$conn) {
    die('Database connection failed. Please check your database settings.');
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ===== LABEL GENERATION MODE =====
if (isset($_GET['sku']) || isset($_GET['skus'])) {
    include __DIR__ . '/includes/printer_html.php';
    
    $printer = new PrinterHTML($conn);
    
    // Handle both single and multiple SKUs the same way
    $skus = isset($_GET['skus']) ? explode(',', $_GET['skus']) : [trim($_GET['sku'] ?? '')];
    $copies = isset($_GET['copies']) ? max(1, (int)$_GET['copies']) : 1;
    
    // Remove empty values
    $skus = array_filter(array_map('trim', $skus));
    
    if (empty($skus)) {
        die('Error: No SKUs provided.');
    }
    
    $result = $printer->printMultipleLabels($skus, $copies);
    
    if (!$result['success']) {
        die('<p style="color:red;">' . $result['message'] . '</p>');
    }
    
    echo $result['html'];
    exit();
}

// ===== UI MODE =====
include __DIR__ . '/includes/header.php';

// Get all active products
$products = mysqli_query($conn, 
    "SELECT p.sku, p.name
     FROM products p
     WHERE p.is_active = 1
     ORDER BY p.name
");
?>

<h2>🖨️ Print Product Labels</h2>

<p>Select products to print labels. Labels will open in a new window - use browser's Print button to save as PDF or print directly.</p>

<div id="printForm">
    <div style="margin-bottom: 15px; padding: 10px; background: #e7f3ff; border-radius: 5px;">
        <label style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
            <strong>Select All</strong>
        </label>
        <div style="margin-top: 10px;">
            <button type="button" onclick="printSelected()" class="btn">
                Print Selected Labels
            </button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">
                    <input type="checkbox" id="selectAllHeader" onchange="toggleAll(this)">
                </th>
                <th>SKU</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($products) === 0): ?>
            <tr><td colspan="3">No products found.</td></tr>
        <?php else: while ($row = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td>
                    <input type="checkbox" name="skus[]" value="<?php echo htmlspecialchars($row['sku']); ?>" 
                           class="product-checkbox">
                </td>
                <td><strong><?php echo htmlspecialchars($row['sku']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>

<script>
/**
 * Toggles product checkboxes on/off
 */
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const isChecked = checkbox.checked;
    checkboxes.forEach(cb => cb.checked = isChecked);
    document.getElementById('selectAllHeader').checked = isChecked;
    document.getElementById('selectAll').checked = isChecked;
}

/**
 * Open print window with selected product SKUs
 */
function printSelected() {
    const selected = Array.from(document.querySelectorAll('.product-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Please select at least one product to print.');
        return;
    }
    
    const skusParam = selected.map(sku => encodeURIComponent(sku)).join(',');
    window.open(`print_labels.php?skus=${skusParam}&copies=1`, '_blank');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

