<?php
// Include database configuration
include "../php/dbconfig.php";

// Start session (if not already started)
session_start();

// Check if user is logged in
if (!isset($_SESSION['recruiter_id'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: checkLogin.php");
    exit();
}

// Check if the form was submitted and if the job ID is provided
if (isset($_POST['close_job']) && isset($_POST['job_id'])) {
    // Get job ID from the form
    $job_id = $_POST['job_id'];

    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Start a transaction
    $conn->begin_transaction();

    // Attempt to delete associated resumes
    $sql_delete_resumes = "DELETE FROM Resume WHERE Job_ID = ?";
    $stmt_delete_resumes = $conn->prepare($sql_delete_resumes);
    $stmt_delete_resumes->bind_param("i", $job_id);

    // Execute the statement
    if (!$stmt_delete_resumes->execute()) {
        // If there's an error, rollback the transaction and display the error
        $conn->rollback();
        echo "Error deleting resumes: " . $conn->error;
        exit();
    }

    // Prepare SQL statement to delete the job
    $sql_delete_job = "DELETE FROM Job WHERE Job_ID = ?";
    $stmt_delete_job = $conn->prepare($sql_delete_job);
    $stmt_delete_job->bind_param("i", $job_id);

    // Execute the statement
    if ($stmt_delete_job->execute()) {
        // If successful, commit the transaction
        $conn->commit();
        echo "<script>
                alert('Job deleted successfully.');
                window.location.href = 'recruiterViewJobs.php';
              </script>";
    } else {
        // If there's an error, rollback the transaction and display the error
        $conn->rollback();
        echo "Error deleting job: " . $conn->error;
    }

    // Close the statements and connection
    $stmt_delete_resumes->close();
    $stmt_delete_job->close();
    $conn->close();
} else {
    // Handle case where form was not submitted correctly
    echo "Invalid request.";
}
?>
