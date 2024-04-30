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
  <link href="./style.css" rel="stylesheet" type="text/css" />
  <link href="./applicantHome.css" rel="stylesheet" type="text/css" />
  <style>
    /* Add custom CSS for the Apply button */
    .accordion {
        margin-top: 5px; /* Adjust margin to create a gap */
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

    /* Adjust checkbox and label style to ensure they display inline */
        .dropdown-menu input[type="checkbox"],
        .dropdown-menu label {
            display: inline-block;
            margin-right: 10px; /* Adjust spacing between options */
        }

     /* Adjust alignment of label with checkbox */
        .dropdown-menu label {
            vertical-align: middle;
        }

  </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom" style="background-color: #7C7474;">
        <a class="navbar-brand" href="#">
          <img class="custom-logo" src="../assets/logo.png" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
            <li class="nav-item">
              <a class="nav-link" href="applicantHome.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="applicantUpdate.php">Update Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="applicantReview.php">Review Application</a>
            </li>
          </ul>
          <form class="form-inline my-2 my-lg-0 me-2" method="post" action="../php/logout.php">
            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Logout</button>
          </form>
        </div>
      </nav> <!--end of navbar-->
    <?php
    // Include database configuration file
include "../php/dbconfig.php";

// Establishing connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
  
    $sql = "SELECT Job_Title, Job_ID, CompanyName,Status FROM AppliedJobsApplicant where email= (?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['Email']);
    if ($stmt){
      if ($stmt->execute()) {
        // Process the result
        $result = $stmt->get_result();

        // Output results
        if ($result->num_rows > 0) {
          
            $count=0;
            echo '<div class="accordion" id="accordionExample">';
            echo "<h2>Applied Jobs:</h2><br>";
            while ($row = $result->fetch_assoc()) {
                    $count++;
                    $jTitle=$row['Job_Title'];
                    $jID=$row['Job_ID'];
                    $jCompanyName=$row['CompanyName'];
                    $jStatus=$row['Status'];

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
                        <strong>'.$jCompanyName.'</strong> <br>
                        
                        Job_ID:' .$jID.'<br>
                        AI Feedback: Sample Feedback <br>
                        Application Feeback: '.$jStatus.'
                        
                        </div>
                        <!--<button type="button" class="btn btn-secondary accordion-apply-btn">Review</button></div>-->
                    </div>';
                    
                    }
                } 
                else {
                    echo "<br>No products found.\n";
                }
                echo '</div>';
    }
  }



?>

  <!-- Bootstrap 5 JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
