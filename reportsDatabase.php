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

// Handle creating a new report
if (isset($_POST['name']) && isset($_POST['hotelId']) && isset($_POST['hotelName']) && isset($_POST['columnNames'])) {
    $reportName = $_POST['name'];
    $hotelId = $_POST['hotelId'];
    $hotelName = $_POST['hotelName'];
    $reportColumns = $_POST['columnNames'];

    // Escape the input to prevent SQL injection
    $reportName = $conn->real_escape_string($reportName);
    $hotelId = $conn->real_escape_string($hotelId);
    $hotelName = $conn->real_escape_string($hotelName);
    $reportColumns = $conn->real_escape_string($reportColumns);

    // Insert the new report into the database
    $insertSql = "INSERT INTO reports (name, hotelId, hotelName, columns) VALUES ('$reportName', '$hotelId', '$hotelName', '$reportColumns')";
    if ($conn->query($insertSql) === TRUE) {
        // report inserted successfully
    } else {
        echo "Error creating report: " . $conn->error;
    }
}

// Retrieve reports from the database based on the provided id
$hotelId = $_GET['hotelId'];
$selectSql = "SELECT id, hotelId, hotelName, name, columns FROM reports WHERE hotelId = $hotelId";

$result = $conn->query($selectSql);

// Return the result as JSON
echo json_encode($result->fetch_all(MYSQLI_ASSOC));

// Close the database connection
$conn->close();
?>
