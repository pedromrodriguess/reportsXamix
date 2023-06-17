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
if (isset($_POST['name']) && isset($_POST['hotelId']) && isset($_POST['hotelName']) && isset($_POST['systemId']) && isset($_POST['systemName']) && isset($_POST['date']) && isset($_POST['company']) && isset($_POST['intervention']) && isset($_POST['function'])) {
   
    $reportName = $_POST['name'];
    $hotelId = $_POST['hotelId'];
    $hotelName = $_POST['hotelName'];
    $systemId = $_POST['systemId'];
    $systemName = $_POST['systemName'];
    $date = $_POST['date'];
    $company = $_POST['company'];
    $intervention = $_POST['intervention'];
    $function = $_POST['function'];

    // Escape the input to prevent SQL injection
    $reportName = $conn->real_escape_string($reportName);
    $hotelId = $conn->real_escape_string($hotelId);
    $hotelName = $conn->real_escape_string($hotelName);
    $systemId = $conn->real_escape_string($systemId);
    $systemName = $conn->real_escape_string($systemName);
    $date = $conn->real_escape_string($date);
    $company = $conn->real_escape_string($company);
    $intervention = $conn->real_escape_string($intervention);
    $function = $conn->real_escape_string($function);

    // Insert the new report into the database
    $insertSql = "INSERT INTO reports (name, hotelId, hotelName, systemId, systemName, date, company, intervention, function) VALUES ('$reportName', '$hotelId', '$hotelName', '$systemId', '$systemName', '$date', '$company', '$intervention', '$function')";
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
        $fileName = $_GET['fileName']; // Get the file name from the URL parameter

        // Validate input and ensure the report exists
        $reportId = $conn->real_escape_string($reportId);
        $selectSql = "SELECT id FROM reports WHERE id = $reportId";
        $result = $conn->query($selectSql);

        if ($result && $result->num_rows > 0) {
            // Update the report in the database
            $updateSql = "UPDATE reports SET pdfFile = '$putData', pdfFileName = '$fileName' WHERE id = $reportId";
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

// Handle deleting a report
if (isset($_GET['reportId'])) {
    $reportId = $_GET['reportId'];
    
    // Escape the input to prevent SQL injection
    $reportId = $conn->real_escape_string($reportId);
    
    // Delete the report from the database
    $sql = "DELETE FROM reports WHERE id = '$reportId'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Report deleted successfully";
    } else {
        echo "Error deleting report: " . $conn->error;
    }
}



// Retrieve reports from the database based on the provided ids
if (isset($_GET['hotelId']) && isset($_GET['systemId'])) {
    $hotelId = $_GET['hotelId'];
    $systemId = $_GET['systemId'];
    $selectSql = "SELECT id, hotelId, hotelName, name, date, company, intervention, function, systemId, systemName, pdfFileName FROM reports WHERE hotelId = $hotelId AND systemId = $systemId";

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
