<?php
// Include database configuration
include "../php/dbconfig.php";

// Check if applied_id and applicant_id are provided via POST
if (!isset($_POST['applied_id']) || !isset($_POST['applicant_id'])) {
    // Redirect back to the previous page or display an error
    header("Location: recruiterReviewApplicant.php");
    exit();
}

// Retrieve AppliedJobs_ID and Applicant_ID from POST
$applied_id = $_POST['applied_id'];
$applicant_id = $_POST['applicant_id'];

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch job title and job ID
$job_info_query = "SELECT Job_Title, Job_ID FROM AppliedJobsApplicant WHERE AppliedJobs_ID = $applied_id";
$job_info_result = $conn->query($job_info_query);
if ($job_info_result->num_rows > 0) {
    $job_info_row = $job_info_result->fetch_assoc();
    $job_title = $job_info_row['Job_Title'];
    $job_id = $job_info_row['Job_ID'];
} else {
    // Handle error if no job information found
    echo "Error: No job information found";
    exit();
}
/*
// Insert applicant details into the Resume table
$insert_sql = "INSERT INTO Resume (JobTitle, Job_ID, Applicant_ID) VALUES ('$job_title', $job_id, $applicant_id)";
if ($conn->query($insert_sql) === TRUE) {
    // Resume record inserted successfully, now delete the application from the AppliedJobsApplicant table

    // Delete the application from the AppliedJobsApplicant table
    $sql_delete = "DELETE FROM AppliedJobsApplicant WHERE AppliedJobs_ID = $applied_id";

    if ($conn->query($sql_delete) === TRUE) {
        // Application deleted successfully, redirect back to the previous page
        header("Location: recruiterReviewApplicant.php");
    } else {
        // Error occurred while deleting the application
        echo "Error deleting application: " . $conn->error;
    }
} else {
    // Error occurred while inserting into the Resume table
    echo "Error inserting into Resume table: " . $conn->error;
}*/
/*
$insert_sql = "INSERT INTO Resume (JobTitle, Job_ID, Applicant_ID) VALUES ('$job_title', $job_id, $applicant_id)";
if ($conn->query($insert_sql) === TRUE) {
    // Resume record inserted successfully, now update the status in the AppliedJobsApplicant table
    $update_sql = "UPDATE AppliedJobsApplicant SET Status = 'Accepted' WHERE AppliedJobs_ID = $applied_id";

    if ($conn->query($update_sql) === TRUE) {
        // Status updated successfully, redirect back to the previous page
        header("Location: recruiterReviewApplicant.php");
    } else {
        // Error occurred while updating the status
        echo "Error updating status: " . $conn->error;
    }
} else {
    // Error occurred while inserting into the Resume table
    echo "Error inserting into Resume table: " . $conn->error;
}*/
$insert_sql = "INSERT INTO Resume (JobTitle, Job_ID, Applicant_ID) VALUES ('$job_title', $job_id, $applicant_id)";

if ($conn->query($insert_sql) === TRUE) {
    // Resume record inserted successfully, now update the status in the AppliedJobsApplicant table
    $update_sql = "UPDATE AppliedJobsApplicant SET Status = 'Accepted' WHERE AppliedJobs_ID = $applied_id";

    if ($conn->query($update_sql) === TRUE) {
        // Status updated successfully, now update the status in the Resume table
        $update_resume_sql = "UPDATE Resume SET Status = 'Accepted' WHERE Job_ID = $job_id AND Applicant_ID = $applicant_id";

        if ($conn->query($update_resume_sql) === TRUE) {
            // Status in the Resume table updated successfully
            header("Location: recruiterReviewApplicant.php");
        } else {
            // Error occurred while updating the status in the Resume table
            echo "Error updating status in Resume table: " . $conn->error;
        }
    } else {
        // Error occurred while updating the status in the AppliedJobsApplicant table
        echo "Error updating status in AppliedJobsApplicant table: " . $conn->error;
    }
} else {
    // Error occurred while inserting into the Resume table
    echo "Error inserting into Resume table: " . $conn->error;
}


$conn->close();
?>
