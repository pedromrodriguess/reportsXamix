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


// Handle updating a report with a PDF
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $putData = file_get_contents("php://input");
    $putData = $conn->real_escape_string($putData); // Sanitize the data

    if (isset($_GET['reportId']) && $putData !== false) {
        $reportId = $_GET['reportId'];

        // Validate input and ensure the report exists
        $reportId = $conn->real_escape_string($reportId);
        $selectSql = "SELECT id FROM reports WHERE id = $reportId";
        $result = $conn->query($selectSql);

        if ($result && $result->num_rows > 0) {
            // Update the report in the database
            $updateSql = "UPDATE reports SET pdfFile = '$putData' WHERE id = $reportId";
            if ($conn->query($updateSql) === TRUE) {
                echo "Report updated successfully with PDF.";
            } else {
                echo "Error updating report: " . $conn->error;
            }
        } else {
            echo "Invalid reportId parameter.";
        }
    } else {
        echo "Invalid request parameters or PDF data.";
    }
}


// Retrieve reports from the database based on the provided id
if (isset($_GET['hotelId'])) {
    $hotelId = $_GET['hotelId'];
    $selectSql = "SELECT id, hotelId, hotelName, name, columns FROM reports WHERE hotelId = $hotelId";

    $result = $conn->query($selectSql);

    // Return the result as JSON
    $reports = $result->fetch_all(MYSQLI_ASSOC);

    // Close the database connection
    $conn->close();

    echo json_encode($reports);
} elseif (isset($_GET['reportId'])) {
    $reportId = $_GET['reportId'];
    $selectSql = "SELECT pdfFile FROM reports WHERE id = $reportId";

    $result = $conn->query($selectSql);

    if ($result && $result->num_rows > 0) {
        $report = $result->fetch_assoc();
        $pdfData = $report['pdfFile'];

        if (!empty($pdfData)) {
            // Set appropriate headers and output the PDF file for download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="report.pdf"');
            echo $pdfData;
        } else {
            echo "PDF file not found for the specified report.";
        }
    } else {
        echo "Invalid reportId parameter.";
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request parameters.";
    $conn->close();
}
?>
