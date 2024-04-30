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
    $companyid = sanitize_input($_POST["companyid"]);

    // Check if email already exists in the database
    $check_query = "SELECT * FROM Recruiters WHERE Email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, show an alert
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href = 'recruiterRegister.html';</script>";
    } else {
        // Email doesn't exist, proceed with insertion
        $sql = "INSERT INTO Recruiters (FirstName, LastName, Email, Password, PhoneNumber, Company_ID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssss", $fname, $lname, $email, $password, $pnumber, $companyid);
            if ($stmt->execute()) {
                // Redirect to index.html
                header("Location: recruiterHome.php");
                exit(); // Make sure no other code is executed after the redirect
            } else {
                // Error occurred while executing the prepared statement
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $stmt->close();
        } else {
            // Error occurred while preparing the SQL statement
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $check_stmt->close();
}

// Close the database connection
$conn->close();
?>
