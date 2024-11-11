<?php
// Start the session
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "web0304");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "Email already exists!";
    } else {
        // Insert user into the database
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>