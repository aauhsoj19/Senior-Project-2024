<?php
// Include database configuration
include "../php/dbconfig.php";

// Check if applied_id is provided via POST
if (!isset($_POST['applied_id'])) {
    // Redirect back to the previous page or display an error
    header("Location: recruiterReviewApplicant.php");
    exit();
}

// Retrieve AppliedJobs_ID from POST
$applied_id = $_POST['applied_id'];

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
/*
// Delete the application from the AppliedJobsApplicant table
$sql_delete = "DELETE FROM AppliedJobsApplicant WHERE AppliedJobs_ID = $applied_id";

if ($conn->query($sql_delete) === TRUE) {
    // Application deleted successfully, redirect back to the previous page
    header("Location: recruiterReviewApplicant.php");
} else {
    // Error occurred while deleting the application
    echo "Error deleting application: " . $conn->error;
}*/
// Update the status to 'Rejected' in the AppliedJobsApplicant table
$update_sql = "UPDATE AppliedJobsApplicant SET Status = 'Rejected' WHERE AppliedJobs_ID = $applied_id";

if ($conn->query($update_sql) === TRUE) {
    // Status updated successfully, redirect back to the previous page
    header("Location: recruiterReviewApplicant.php");
} else {
    // Error occurred while updating the status
    echo "Error updating status: " . $conn->error;
}


$conn->close();
?>
