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

// Handle creating a new system
if (isset($_POST['name']) && isset($_POST['hotelId']) && isset($_POST['hotelName'])) {
    $systemName = $_POST['name'];
    $hotelId = $_POST['hotelId'];
    $hotelName = $_POST['hotelName'];

    // Escape the input to prevent SQL injection
    $systemName = $conn->real_escape_string($systemName);
    $hotelId = $conn->real_escape_string($hotelId);
    $hotelName = $conn->real_escape_string($hotelName);

    // Insert the new system into the database
    $insertSql = "INSERT INTO systems (name, hotelId, hotelName) VALUES ('$systemName', '$hotelId', '$hotelName')";
    if ($conn->query($insertSql) === TRUE) {
        // Hotel inserted successfully
    } else {
        echo "Error creating system: " . $conn->error;
    }
}

// Retrieve systems from the database based on the provided ids
if (isset($_GET['hotelId'])) {
    $hotelId = $_GET['hotelId'];
    $selectSql = "SELECT id, name, hotelId, hotelName FROM systems WHERE hotelId = $hotelId";

    $result = $conn->query($selectSql);

    // Return the result as JSON
    $reports = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($reports);
}

// Handle deleting a system
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['systemId'])) {
        $systemId = $_GET['systemId'];
        
        // Escape the input to prevent SQL injection
        $systemId = $conn->real_escape_string($systemId);
        
        // Delete the system from the database
        $sql = "DELETE FROM systems WHERE id = '$systemId'";
        
        if ($conn->query($sql) === TRUE) {
            echo "System deleted successfully";
        } else {
            echo "Error deleting system: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
