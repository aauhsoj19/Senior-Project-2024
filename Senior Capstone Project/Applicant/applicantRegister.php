<?php
// Include database configuration file
include "../php/dbconfig.php";

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
    $resume = sanitize_input($_POST["resume"]);

    // Check if email already exists in the database
    $check_query = "SELECT * FROM Applicants WHERE Email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, show an alert
        echo "<script>alert('Email already exists. Please use a different email.'); 
        window.location.href = 'applicantRegister.html';</script>";
    }
    else {
        // Email doesn't exist, proceed with insertion
    // SQL query to insert applicant data into the database
  $sql = "insert into Applicants (FirstName,LastName,Email,Password,PhoneNumber,ResumeFile) Values (?, ?, ?, ?, ?,?)";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("ssssss", $fname, $lname, $email, $password, $pnumber, $resume);
       
        // Execute the prepared statement
        if ($stmt->execute()) {
            
            header("Location: ../index.html");
            exit(); 
        } 
        else {
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

}
// Close the database connection
$conn->close();

?>