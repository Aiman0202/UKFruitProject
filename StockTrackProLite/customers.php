<?php
/* customers.php  –  List + simple actions */
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

/* Fetch all customers (alphabetical) */
$result = mysqli_query($conn, "SELECT id, name, phone, email, address
    FROM customers
    WHERE is_active = 1
    ORDER BY name ASC
");

/* Flash message */
$flash = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $flash = '<p class="notice">Customer deleted.</p>';
}
?>
<h2>Customers</h2>

<div style="display:flex; align-items:center; gap: 500px;0px; margin-bottom:12px;">

<a href="customer_add.php" class="btn">+ Add Customer</a>

<input 
    type="text" 
    id="searchName" 
    placeholder="Search customer name..." 
    style="padding:6px; width:250px; margin-bottom:12px;"
>

</div>

<?php echo $flash; ?>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th class="wide">Address</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr data-customer="<?php echo strtolower($row['name'] ?? ''); ?>">
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['address'])); ?></td>
            <td>
                <a href="customer_edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="customer_delete.php?id=<?php echo $row['id']; ?>"
                   onclick="return confirm('Delete this customer?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
        <tr><td colspan="5">No customers found.</td></tr>
<?php endif; ?>
    </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php'; ?>