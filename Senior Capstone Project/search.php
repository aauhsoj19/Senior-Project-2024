<?php

session_start();

try{
  if(isset($_SESSION['logged_in'] )){
    if($_SESSION['logged_in']==TRUE){
      $loggedin=$_SESSION['logged_in'];
    }
    else{
      throw new Exception("Failed to log in");
    }
  }
  else {
    throw new Exception("Failed to log in");
  }
}
catch(Exception $e)
{
  die($e->getMessage() . '. Please log in from the homepage.');

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Artificial J.A.M.A</title>
  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <link href="style.css" rel="stylesheet" type="text/css" />
  <link href="applicantHome.css" rel="stylesheet" type="text/css" />
  <style>
    /* Add custom CSS for the Apply button */
    .accordion {
      margin-top: 100px; /* Adjust margin to create a gap */
    }

    .accordion-button {
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }
    .accordion-button:last-child {
      border-bottom-left-radius: 0;
      border-bottom-right-radius: 0;
    }

    /* Apply button styling */
    .accordion-apply-btn {
      border-radius: 20px; /* Rounded border */
      width: 200px; /* Adjust width */
      margin: 0 auto; /* Center button */
      display: block; /* Ensure it's a block element */
      background-color: #B6AAAA; /* Apply button color */
      color: #ffffff; /* Text color */
      border-color: #B6AAAA; /* Border color */
    }
  </style>
</head>

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
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // SQL query to select job data from the database
    $sql = "SELECT JobTitle, JobDescription, JobQualifications, JobLocation, Filter, CompanyName FROM Job";

    // Sanitize and validate form data
    $filter1 = isset($_GET["filter1"]) ? sanitize_input($_GET["filter1"]) : null;
    $filter2 = isset($_GET["filter2"]) ? sanitize_input($_GET["filter2"]) : null;
    $filter3 = isset($_GET["filter3"]) ? sanitize_input($_GET["filter3"]) : null;
    $searchKeyword = isset($_GET["search"]) ? sanitize_input($_GET["search"]) : null;

    // Check if search keyword is provided
    if (!empty($searchKeyword)) {
        // If search keyword is not empty, filter based on it
        $searchKeyword = '%' . $searchKeyword . '%';
        $sql .= " WHERE JobTitle LIKE ?";
    } else {
        // If search keyword is not provided, show all jobs
        $searchKeyword = "%"; // Match all job titles
    }

    // Check if filter is provided
    $filter_array = array($filter1, $filter2, $filter3);
    $checked_array = [];

    foreach ($filter_array as $e) {
        if (!is_null($e) && !empty($e)) {
            $checked_array[] = $e;
            echo 'added' . $e;
        }
    }
    //print_r($checked_array);

    // If both search keyword and some filter values are given
    if ($searchKeyword!='%' && count($checked_array) > 0) {
        $sql .= " AND Filter IN (";
        $placeholders = implode(",", array_fill(0, count($checked_array), "?"));
        $sql .= $placeholders;
        $sql .= ")";
    } elseif ($searchKeyword=='%' && count($checked_array) > 0) {
        $sql .= " WHERE Filter IN (";
        $placeholders = implode(",", array_fill(0, count($checked_array), "?"));
        $sql .= $placeholders;
        $sql .= ")";
    }


    #echo $sql; // For testing purposes
}

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
  // Handle the error
  echo "Error preparing statement: " . $conn->error;
} else {
  // Initialize parameter types string
  $paramTypes = '';

  // Initialize parameters array
  $params = array();

  // Bind search keyword parameter
  if ($searchKeyword != '%') {
      $paramTypes .= 's'; // Append 's' for string type
      $params[] = &$searchKeyword; // Add search keyword to parameters array by reference
  }

  // Bind filter parameters
  if (!empty($checked_array)) {
      // Append 's' for each filter parameter
      $paramTypes .= str_repeat('s', count($checked_array)); 

      // Push each filter parameter onto parameters array by reference
      foreach ($checked_array as &$value) {
          $params[] = &$value;
      }
  }

  // Prepend the statement with 'bind_param' parameters
  $bindParams = array_merge(array($paramTypes), $params);

  // Bind parameters to the prepared statement
  call_user_func_array(array($stmt, 'bind_param'), $bindParams);
}



    // Execute the prepared statement
    if ($stmt->execute()) {
        // Process the result
        $result = $stmt->get_result();

        // Output results
        if ($result->num_rows > 0) {

            $count=0;
            echo '<body>
            <script>
                function redirectToApplicantHome() {
                    window.location.href = "Applicant/applicantHome.php";
                }
            </script>

            <button type="button" onclick="redirectToApplicantHome()">Back</button>

            <div class="accordion" id="accordionExample">';
            while ($row = $result->fetch_assoc()) {
                    $count++;
                    $jTitle=$row['JobTitle'];
                    $jDesc=$row['JobDescription'];
                    $jQualif=$row['JobQualifications'];
                    $jLocation=$row['JobLocation'];
                    $jType=$row['Filter'];
                    $jCompanyName=$row['CompanyName'];

                    echo '

                    <!-- Accordion -->
                    <div class="accordion-item">
                    <h2 class="accordion-header" id="heading' . $count.'"> 
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$count.'" aria-expanded="true" aria-controls="collapse'. $count.'"> 
                        Job Title: '. $jTitle .'
                        </button>
                    </h2>
                    <div id="collapse'. $count.'" class="accordion-collapse collapse" aria-labelledby="heading'. $count.'">
                        <div class="accordion-body">
                        <strong>"Company Logo"</strong>  &nbsp; <strong>'.$jCompanyName.'</strong> <br>
                        JobLocation:' .$jLocation.'<br>
                        JobDescription:' .$jDesc.'<br>
                        JobQualification:' .$jQualif.'<br>
                        </div>
                        <button type="button" class="btn btn-secondary accordion-apply-btn">Apply</button>
                    </div>
                    </div>';
                    echo "Results for :" . $searchKeyword . "  " . $filter1 . " " . $filter2 . " " . $filter3 ."<br>\n";
                }
                } 
                else {
                    echo "<br>No products found.\n";
                }
                echo '</div>';
            }
            else {
    echo "<br>Something wrong with the database or SQL query.\n" ;
    echo "<br>Error message: " . $conn->$error;
  }

    $result->free_result();
   //



    // Close the statement
    $stmt->close();


// Close the database connection
$conn->close();


?>
<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
