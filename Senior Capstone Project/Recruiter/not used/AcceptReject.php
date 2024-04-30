<?php
// Include database configuration
include "../php/dbconfig.php";

// Start session (if not already started)
session_start();

try {
    // Check if user is logged in
    if (!isset($_SESSION['recruiter_id'])) {
        throw new Exception("User not logged in.");
    }

    // Check if Job_ID is provided via POST
    if (!isset($_POST['job_id'])) {
        throw new Exception("Missing job_id parameter");
    }

    // Retrieve Job_ID from POST
    $job_id = $_POST['job_id'];
    $job_title = $_POST['Job_Title'];

    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if Accept or Reject action is performed
    if (isset($_POST['accept'])) {
        // Accept applicant
        $applicant_id = $_POST['applicant_id'];

        // Retrieve JobTitle associated with the Job_ID
        $jobTitleQuery = "SELECT JobTitle FROM Job WHERE Job_ID = $job_id";
        $jobTitleResult = $conn->query($jobTitleQuery);
        if ($jobTitleResult->num_rows > 0) {
            $row = $jobTitleResult->fetch_assoc();
            $jobTitle = $row['JobTitle'];
        } else {
            throw new Exception("Job title not found for the provided job ID.");
        }

        // Insert applicant data into Resume table along with JobTitle
        $insert_sql = "INSERT INTO Resume (JobTitle, Job_ID, Applicant_ID)
                       VALUES ('$jobTitle', $job_id, $applicant_id)";

        if ($conn->query($insert_sql) === TRUE) {
            // Applicant accepted, now delete from AppliedJobsApplicant
            $delete_sql = "DELETE FROM AppliedJobsApplicant
                           WHERE Job_ID = $job_id AND Applicant_ID = $applicant_id";

            if ($conn->query($delete_sql) === TRUE) {
                // Redirect with success message
                header("Location: recruiterReviewApplicant.php?accept=success&job_id=$job_id");
                exit();
            } else {
                throw new Exception("Error deleting record: " . $conn->error);
            }
        } else {
            throw new Exception("Error inserting record: " . $conn->error);
        }
    } elseif (isset($_POST['reject'])) {
        // Reject applicant
        $applicant_id = $_POST['applicant_id'];

        // Delete applicant from AppliedJobsApplicant
        $delete_sql = "DELETE FROM AppliedJobsApplicant
                       WHERE Job_ID = $job_id AND Applicant_ID = $applicant_id";

        if ($conn->query($delete_sql) === TRUE) {
            // Redirect with success message
            header("Location: recruiterReviewApplicant.php?reject=success&job_id=$job_id");
            exit();
        } else {
            throw new Exception("Error deleting record: " . $conn->error);
        }
    }
} catch (Exception $e) {
    // Handle exceptions here
    echo "Error: " . $e->getMessage();
    exit();
}
?>
