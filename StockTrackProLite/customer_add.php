<?php
/* customer_add.php – Create a new customer */
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

/* ------- Handle INSERT ------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    mysqli_query($conn, 
       "INSERT INTO customers (name, phone, email, address)
        VALUES ('$name', '$phone', '$email', '$address')
    ");

    header('Location: customers.php?msg=added');
    exit();
}
?>
<h2>Add Customer</h2>

<form action="customer_add.php" method="post">
    <label>Name:
        <input type="text" name="name" required>
    </label>

    <label>Phone:
        <input type="text" name="phone">
    </label>

    <label>Email:
        <input type="text" name="email">
    </label>

    <label>Address:
        <textarea name="address" rows="3"></textarea>
    </label>

    <p>
        <input type="submit" value="Add Customer">
        <a href="customers.php" class="btn-secondary">Cancel</a> <!--secondary 'cancel button' styling-->
    </p>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
