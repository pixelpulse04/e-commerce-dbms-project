<?php
session_start();
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure we have the required POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['action'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];

    // Get the user's cart ID
    $sql = "SELECT cart_id FROM Carts WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart) {
        $cart_id = $cart['cart_id'];

        if ($action === 'increase') {
            // Increase quantity by 1
            $sql = "UPDATE CartItems SET quantity = quantity + 1 WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);

        } elseif ($action === 'decrease') {
            // Decrease quantity by 1
            $sql = "SELECT quantity FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item && $item['quantity'] > 1) {
                // If quantity > 1, decrease quantity
                $sql = "UPDATE CartItems SET quantity = quantity - 1 WHERE cart_id = :cart_id AND product_id = :product_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
            } else {
                // If quantity is 1, remove the item
                $sql = "DELETE FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
            }
        }
    }
}

// Redirect back to the cart page
header('Location: cart.php');
exit();
?>