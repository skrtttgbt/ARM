<?php
// Script to create an admin user
define('BASEPATH', true);

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'tsug3';

// Connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin user details
$email = 'admin@gmail.com';
$password = md5('admin'); // MD5 hash of 'admin'
$first_name = 'Admin';
$last_name = 'User';
$date = date("F j, Y");

// Insert admin user
$sql = "INSERT INTO users (level, status, image, first_name, last_name, email, mobile, password, created_at, updated_at) 
        VALUES (0, 1, 'default.png', '$first_name', '$last_name', '$email', '', '$password', '$date', '" . time() . "')";

if ($conn->query($sql) === TRUE) {
    echo "Admin user created successfully with email: admin@gmail.com and password: admin\n";
} else {
    echo "Error creating admin user: " . $conn->error . "\n";
}

$conn->close();
?>