<?php
session_start();
// Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "web0304";

// // Create a connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

include('db_connection.php');

function getCart($user_id) {
    global $pdo;
    
    // Retrieve the user's cart items along with product information
    $sql = "SELECT ci.product_id, ci.quantity, p.product_name, p.price
            FROM CartItems ci
            JOIN Carts c ON ci.cart_id = c.cart_id
            JOIN Products p ON ci.product_id = p.product_id
            WHERE c.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index1.html');
    exit();
}

$user_id = $_SESSION['user_id'];

$cartItems = getCart($user_id);



// // If delete button is clicked, delete the product from the cart
// if (isset($_POST['delete'])) {
//     $cart_id = $_POST['cart_id'];

//     $delete_sql = "DELETE FROM cart WHERE cart_id = $cart_id";
//     if ($conn->query($delete_sql) === TRUE) {
//         echo "<script>alert('Product deleted successfully!');</script>";
//     } else {
//         echo "Error deleting product: " . $conn->error;
//     }
// }

// // Fetch all products from the cart table
// $c_id=$_POST['cart_id'];
// $sql = "SELECT * FROM cart WHERE cart_id=$c_id";
// $result = $conn->query($sql);

// Initialize total price
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Your Cart</h1>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <td>Add/Delete</td>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cartItems)): ?>
                <?php foreach($cartItems as $item): ?>
                    <tr>
                        <td data-label="Product Name"><?php echo $item['product_name']; ?></td>
                        <td data-label="Price">Rs. <?php echo number_format(($item['price']*$item['quantity']), 2); ?></td>
                        <td data-label="quantity"><?php echo $item['quantity']; ?></td>
                        <td data-label="add/delete">
                            <form action="update_cart_quantity.php" method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit">+</button>
                            </form>
                            <form action="update_cart_quantity.php" method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit">-</button>
                            </form>
                        </td>
                        <td data_label="Delete">
                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" name="delete" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total_price += ($item['price']*$item['quantity']) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" data-label="Your Cart">Your cart is empty.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Total Price: Rs. <?php echo number_format($total_price, 2); ?></h2>
    <div class="goback">
        <button type="submit" onclick="goback()" id="goback-btn">Go back</button>
    </div>
</body>
<script>
    function goback(){
        window.history.back();
    }
</script>
</html>

<!-- <?php
// Close the connection
$conn->close();
?> -->
