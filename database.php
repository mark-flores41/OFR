<?php
$host = 'localhost'; // Database host
$username = 'root';  // Database username
$password = '';      // Database password
$dbname = 'Online_reservation'; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn ->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>