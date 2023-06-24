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
        // Get the hotel ID of the newly inserted hotel
        $hotelId = $conn->insert_id;

        // Insert the default systems into the systems table
        $defaultSystems = array(
            "Sistema Automático Deteção de Incêndio",
            "Sistema Automático Deteção de Gás",
            "Grupo Bombagem",
            "Iluminação de Emergência",
            "Sistema de Controlo de Fumos",
            "Sistema Fixo de Extinção de Hotte",
            "Sinalização de Segurança",
            "Portas Corta Fogo",
            "Extintores",
            "Carreteis",
            "Hidrantes",
            "Sistema Fixo de Extinção por Água - Sprinklers",
            "Todos os Sistemas",
            "Sistema Automático Deteção CO"
        );

        foreach ($defaultSystems as $systemName) {
            // Escape the input to prevent SQL injection
            $systemName = $conn->real_escape_string($systemName);

            // Insert the system with the associated hotel ID and hotel name
            $insertSystemSql = "INSERT INTO systems (name, hotelId, hotelName) VALUES ('$systemName', '$hotelId', '$hotelName')";
            if ($conn->query($insertSystemSql) !== TRUE) {
                echo "Error creating system: " . $conn->error;
            }
        }
    } else {
        echo "Error creating hotel: " . $conn->error;
    }
}


// Handle deleting a system
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_GET['hotelId'])) {
        $hotelId = $_GET['hotelId'];
        
        // Escape the input to prevent SQL injection
        $hotelId = $conn->real_escape_string($hotelId);
        
        // Delete the hotel from the database
        $sql = "DELETE FROM hotels WHERE id = '$hotelId'";
        
        if ($conn->query($sql) === TRUE) {
            echo "Hotel deleted successfully";
        } else {
            echo "Error deleting hotel: " . $conn->error;
        }
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
