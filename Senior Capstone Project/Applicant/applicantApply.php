<?php

session_start();
 $fname;
 $Applicant_ID;
 $lname;
 $email;
 $pnumber;
 $resume=NULL;


// Include database configuration
include "../php/dbconfig.php";
// Establishing connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}

// Check if email already exists in the database
$sql = "SELECT * FROM Applicants WHERE Email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind parameters
    $stmt->bind_param("s", $_SESSION['Email']);
    
    // Execute the prepared statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            // Fetch data
            while ($row = $result->fetch_assoc()) {
                $fname = $row['FirstName'];
                $lname = $row['LastName'];
                $Applicant_ID = $row['Applicant_ID'];
                $email = $row['Email'];
                $pnumber = $row['PhoneNumber'];
                $resume = $row['ResumeFile']; // Assuming 'ResumeFile' is the column name for the resume
            }
        } else {
            echo 'No entries';
            exit();
        }
        $stmt->close();
    } else {
        // Error occurred while executing the prepared statement
        echo "Error executing statement: " . $stmt->error;
        exit();
    }
} else {
    // Error in prepared statement
    echo "Error in prepared statement: " . $conn->error;
    exit();
}
$sql = "SELECT * FROM AppliedJobsApplicant WHERE Email = ? and Job_ID = ?";
$check_stmt = $conn->prepare($sql);
$check_stmt->bind_param("si", $_SESSION['Email'],$_POST['jobId']);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
        // Email already exists, show an alert
        echo "Job already applied.";
        exit();
    }
else{
// Insert into AppliedJobsApplicant
$sql = "INSERT INTO AppliedJobsApplicant 
        (Job_ID, Job_Title, CompanyName, Applicant_ID, ApplicantName, LastName, Email, PhoneNumber, Resume, Recruiter_ID)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind parameters
    $stmt->bind_param("issssssssi", $_POST['jobId'], $_POST['jobTitle'], $_POST['companyName'], $Applicant_ID, $fname, $lname, $email, $pnumber, $resume, $_POST['recruiterId']);
    
    // Execute the prepared statement
    if ($stmt->execute()) {
        echo 'Applied Success';
        exit();
    } else {
        // Error executing statement
        echo "Error inserting record: " . $stmt->error;
    }
} else {
    // Error in prepared statement
    echo "Error in prepared statement: " . $conn->error;
}
}
// Close connection
$conn->close();
?>
