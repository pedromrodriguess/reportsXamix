<?php
// Database connection settings
$host = 'localhost';
$username = 'u780123187_admin';
$password = 'Password123';
$database = 'u780123187_xamix_reports';

// Create a new database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle creating a new hotel
if (isset($_POST['name'])) {
    $hotelName = $_POST['name'];

    // Escape the input to prevent SQL injection
    $hotelName = $conn->real_escape_string($hotelName);

    // Insert the new hotel into the database
    $insertSql = "INSERT INTO hotels (name) VALUES ('$hotelName')";
    if ($conn->query($insertSql) === TRUE) {
        // Hotel inserted successfully
    } else {
        echo "Error creating hotel: " . $conn->error;
    }
}

// Retrieve hotels from the database
$selectSql = "SELECT id, name FROM hotels";
$result = $conn->query($selectSql);

// Return the result as JSON
echo json_encode($result->fetch_all(MYSQLI_ASSOC));

// Close the database connection
$conn->close();
?>