<?php
function getProducts() {
    global $pdo;
    $sql = "SELECT * FROM Products";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $sql = "DELETE FROM CartItems WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cart_id' => $cart_id, 'product_id' => $product_id]);
    }
}

function clearCart($user_id) {
    global $pdo;
    
    // Get the user's cart ID
    $sql = "SELECT cart_id FROM Carts WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cart) {
        $cart_id = $cart['cart_id'];
        // Remove all items from the user's cart
        $sql = "DELETE FROM CartItems WHERE cart_id = :cart_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['cart_id' => $cart_id]);
    }
}
?>