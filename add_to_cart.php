<?php
session_start();


include('db_connection.php');

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    header('Location: index1.html');
    exit();
}

function addToCart($user_id, $product_id, $quantity) {
    global $pdo;
    
    // Check if the user already has a cart
    $sql = "SELECT cart_id FROM Carts WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cart) {
        // If no cart exists for the user, create a new cart
        $cart_id = createCart($user_id);
    } else {
        // Use the existing cart ID
        $cart_id = $cart['cart_id'];
    }
    
    // Check if the product is already in the user's cart
    $sql = "SELECT * FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        // If the product is already in the cart, update the quantity
        $newQuantity = $cartItem['quantity'] + $quantity;
        $sql = "UPDATE CartItems SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['quantity' => $newQuantity, 'cart_id' => $cart_id, 'product_id' => $product_id]);
    } else {
        // If the product is not in the cart, insert it
        $sql = "INSERT INTO CartItems (cart_id, product_id, quantity) 
                VALUES (:cart_id, :product_id, :quantity)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id, 'quantity' => $quantity]);
    }

    // $sql = "INSERT INTO CartItems (cart_id, product_id) 
    //             VALUES (:cart_id, :product_id)";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
}

function createCart($user_id) {
    global $pdo;
    
    // Create a new cart for the user
    $sql = "INSERT INTO Carts (user_id) VALUES (:user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    
    // Return the newly created cart ID
    return $pdo->lastInsertId();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];
    
    // Get the product ID and quantity from the form submission
    $product_id = $_POST['product_id'];

    $quantity = $_POST['quantity'];
    
    // Add the product to the user's cart
    addToCart($user_id, $product_id, $quantity);
    
    // Redirect back to the product listing or cart page
    header('Location: cart.php');
    exit();
}

// Close the database connection
$conn->close();
?>