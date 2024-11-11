<?php
session_start();
include('db_connection.php');

function removeFromCart($user_id, $product_id) {
    global $pdo;
    
    // Get the user's cart ID
    $sql = "SELECT cart_id FROM Carts WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cart) {
        $cart_id = $cart['cart_id'];
        // Remove the product from the user's cart
        $sql = "DELETE FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
    }
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];
    
    // Get the product ID from the form submission
    $product_id = $_POST['product_id'];
    
    // Remove the product from the user's cart
    removeFromCart($user_id, $product_id);
    
    // Redirect back to the cart page
    header('Location: cart.php');
    exit();
}
?>