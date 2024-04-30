<?php
// Include database configuration file
include "php/dbconfig.php";

// Establishing connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to safely handle user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $fname = sanitize_input($_POST["fname"]);
    $lname = sanitize_input($_POST["lname"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);
    $pnumber = sanitize_input($_POST["pnumber"]);

    // File upload handling for resume
    $resume_file = $_FILES["resume"]["name"];
    $resume_temp_file = $_FILES["resume"]["tmp_name"];
    $resume_file_type = $_FILES["resume"]["type"];
    $upload_directory = "resume_uploads/";

    // Move uploaded file to permanent location
    $resume_path = $upload_directory . $resume_file;
    move_uploaded_file($resume_temp_file, $resume_path);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert applicant data into the database
    $sql = "INSERT INTO Applicants (FirstName, LastName, Password, Email, PhoneNumber, ResumeFile) VALUES (?, ?, ?, ?, ?, ?)";
    
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("ssssss", $fname, $lname, $hashed_password, $email, $pnumber, $resume_path);
        
        // Execute the prepared statement
        if ($stmt->execute()) {
            // Registration successful, redirect to applicantHome.html
            header("Location: applicantHome.html");
            exit();
        } else {
            // Error occurred while executing the prepared statement
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error occurred while preparing the SQL statement
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
