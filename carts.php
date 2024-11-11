<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopsite";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If delete button is clicked, delete the product from the cart
if (isset($_POST['delete'])) {
    $cart_id = $_POST['cart_id'];

    $delete_sql = "DELETE FROM cart WHERE cart_id = $cart_id LIMIT 1";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Product deleted successfully!');</script>";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

// Fetch all products from the cart table
$c_id=$_POST['cart_id'];
$sql = "SELECT * FROM cart WHERE cart_id=$c_id";
$result = $conn->query($sql);

// Initialize total price
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="cartstyle.css">
</head>
<body>
    <h1>Your Cart</h1>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Product Name"><?php echo $row['product_name']; ?></td>
                        <td data-label="Price">Rs. <?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                <button type="submit" name="delete" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total_price += $row['price']; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Your cart is empty.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Total Price: Rs. <?php echo number_format($total_price, 2); ?></h2>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
