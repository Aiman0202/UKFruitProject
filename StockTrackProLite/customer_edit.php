<?php
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    mysqli_query($conn, 
       "UPDATE customers
        SET name='$name',
            phone='$phone',
            email='$email',
            address='$address'
        WHERE id=$id
    ");

    header('Location: customers.php?msg=updated');
    exit();
}

$res = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
if (!$row = mysqli_fetch_assoc($res)) {
    echo '<p class="notice">Customer not found.</p>';
    include __DIR__ . '/includes/footer.php';
    exit();
}
?>
<h2>Edit Customer</h2>

<form action="customer_edit.php?id=<?php echo $id; ?>" method="post">
    <label>Name:
        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
    </label>

    <label>Phone:
        <input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>">
    </label>

    <label>Email:
        <input type="text" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
    </label>

    <label>Address:
        <textarea name="address" rows="3"><?php echo htmlspecialchars($row['address']); ?></textarea>
    </label>

    <p>
        <input type="submit" value="Save">
        <a href="customers.php"  class="btn-secondary">Cancel</a> <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
