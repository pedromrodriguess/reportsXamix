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

// Handle creating a new type
if (isset($_POST['name']) && isset($_POST['hotelId']) && isset($_POST['hotelName'])) {
    $typeName = $_POST['name'];
    $hotelId = $_POST['hotelId'];
    $hotelName = $_POST['hotelName'];

    // Escape the input to prevent SQL injection
    $typeName = $conn->real_escape_string($typeName);
    $hotelId = $conn->real_escape_string($hotelId);
    $hotelName = $conn->real_escape_string($hotelName);

    // Insert the new type into the database
    $insertSql = "INSERT INTO types (name, hotelId, hotelName) VALUES ('$typeName', '$hotelId', '$hotelName')";
    if ($conn->query($insertSql) === TRUE) {
        // Hotel inserted successfully
    } else {
        echo "Error creating type: " . $conn->error;
    }
}

// Retrieve hotels from the database
$selectSql = "SELECT id, name, hotelId, hotelName FROM types";
$result = $conn->query($selectSql);

// Return the result as JSON
echo json_encode($result->fetch_all(MYSQLI_ASSOC));

// Close the database connection
$conn->close();
?>
