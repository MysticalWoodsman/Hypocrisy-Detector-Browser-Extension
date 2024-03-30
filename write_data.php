<?php
// Example PHP script for writing data to MySQL table

// Get data from AJAX request
$data = $_POST['data'];

// Perform database operations (insert into MySQL table)
// Example MySQL connection and insertion
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape the data to prevent SQL injection
$data = $conn->real_escape_string($data);

// Insert data into MySQL table
$sql = "INSERT INTO your_table (column_name) VALUES ('$data')";

if ($conn->query($sql) === TRUE) {
    echo "Data written to MySQL table successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

