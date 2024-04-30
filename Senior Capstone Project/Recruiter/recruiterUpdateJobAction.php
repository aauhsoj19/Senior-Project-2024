<?php
// Include database configuration
include "../php/dbconfig.php";

// Start session (if not already started)
session_start();

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if user is logged in
    if (!isset($_SESSION['recruiter_id'])) {
        // Redirect to login page or handle unauthorized access
        header("Location: checkLogin.php");
        exit();
    }

    // Fetch form data
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id'])) {
        // Fetch job details based on job ID
        $job_id = $_POST['job_id'];
        $jobTitle = $_POST['jtitle'];
        $jobLocation = $_POST['jlocation'];
        $jobDescription = $_POST['jdescription'];
        $jobQualification = $_POST['jqualification'];
        $keywords = $_POST['keywords'];
        $logo = $_FILES['logo']['name']; // Retrieve logo file name

        // Prepare SQL statement to update job details
        $sql = "UPDATE Job SET JobTitle=?, JobLocation=?, JobDescription=?, JobQualifications=?, Keywords=?, JobLogo=? 
        WHERE Job_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $jobTitle, $jobLocation, $jobDescription, $jobQualification, $keywords, $logo, $job_id);

        // Upload logo file if provided
        if (!empty($_FILES['logo']['tmp_name'])) {
            $target_dir = "../uploads/"; // Directory where logo files will be uploaded
            $target_file = $target_dir . basename($_FILES['logo']['name']);
            move_uploaded_file($_FILES['logo']['tmp_name'], $target_file);
        }

        // Execute SQL statement
        if ($stmt->execute()) {
            // Job details updated successfully
            $response = array("success" => "Job details updated successfully.");
            echo json_encode($response);
        } else {
            throw new Exception("Error updating job details: " . $conn->error);
        }
    } else {
        // No job ID provided
        throw new Exception("No job ID provided.");
    }
} catch (Exception $e) {
    // Handle exceptions here
    $response = array("error" => $e->getMessage());
    echo json_encode($response);
    exit();
}
?>
