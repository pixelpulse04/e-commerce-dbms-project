<?php
// Start the session
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "web0304");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check the user credentials
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        // Verify the password
        // if (password_verify($password, $user['password'])) {
        if ($password === $user['password']) {
        $_SESSION['user'] = $user['username'];
            $_SESSION['user_id']=$user['user_id'];
            header('Location: index.html');
            exit();
            echo "Login successful! Welcome, " . $_SESSION['user'];
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this email!";
    }
}

$conn->close();
?>