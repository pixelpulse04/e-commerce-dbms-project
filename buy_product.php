<?php
// Database connection settings
$servername = "localhost";  // XAMPP uses 'localhost' by default
$username = "root";         // Default MySQL username for XAMPP
$password = "";             // Default is an empty password
$dbname = "shopsite";   // Replace with your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the product data from the POST request
$c_id=$_POST['c_id'];
$product_id = $_POST['product_id'];
$product_name = $_POST['name'];
$product_price = $_POST['price'];

// Insert data into the 'product' table
$sql = "INSERT INTO cart (cart_id,product_id,product_name,price) VALUES ('$c_id','$product_id','$product_name','$product_price')";

if ($conn->query($sql) === TRUE) {
    //echo "<script>alert('Product purchased successfully!'); window.location.href='webproject34/tryhome.html';</script>";
    echo"<script>window.location.href='Homepage.html';</script>";
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>