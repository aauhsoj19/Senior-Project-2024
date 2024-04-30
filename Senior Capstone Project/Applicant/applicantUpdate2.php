<?php

session_start();
// Handle form submission and update logic here
// For example, you can access form data using $_POST superglobal
function sanitize_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Simulate processing (replace this with your actual update logic)
//sleep(2); // Simulate delay
//$response = "Update successful!";

// Return response to client
//echo $response;



include "../php/dbconfig.php";
// Establishing connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and validate form data
  $fname = sanitize_input($_POST["fname"]);
  $lname = sanitize_input($_POST["lname"]);
  $email = sanitize_input($_POST["email"]);
  $password = sanitize_input($_POST["password"]);
  $pnumber = sanitize_input($_POST["pnumber"]);
  #$resume = sanitize_input($_POST["resume"]);


  $sql = 
  "UPDATE Applicants 
  SET ";

      if (isset($fname)) {
        $sql .= "FirstName = ?, ";
        $update_params[] = $fname;
      }
      if (isset($lname)) {
        $sql .= "LastName = ?, ";
        $update_params[] = $lname;
      }
      if (isset($password)) {
        $sql .= "Password = ?, ";
        $update_params[] = $password;
      }
      if (isset($pnumber)) {
        $sql .= "PhoneNumber = ?, ";
        $update_params[] = $pnumber;
      }
      //if ($resume != $row['ResumeFile']) {
        //$sql .= "ResumeFile = ?, ";
        //$update_params[] = $resume;
     // }

      $sql = rtrim($sql, ", ");

      $sql .= " WHERE Email = ?";
      $update_params[] = $_SESSION['Email'];
      echo $sql;

      $stmt = $conn->prepare($sql);
      if ($stmt) {
          $param_types = str_repeat('s', count($update_params)); // assuming all parameters are strings
          $stmt->bind_param($param_types, ...$update_params);
      
          // Execute statement
          if ($stmt->execute()) {
              // Update successful
              echo "Record updated successfully.";
          } else {
              // Update failed
              echo "Error updating record: " . $stmt->error;
          }
      
          // Close statement
          $_SESSION['FirstName']=$fname;
          $stmt->close();
      } else {
          // Error in prepared statement
          echo "Error in prepared statement: " . $conn->error;
      }
    } 
    
    $conn->close();
 ?>
