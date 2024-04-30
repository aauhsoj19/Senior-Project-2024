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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="../style.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom" style="background-color: #7C7474;">
      <a class="navbar-brand" href="applicantHome.php">
        <img class="custom-logo" src="../assets/logo.png" alt="Logo">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="applicantHome.php">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="applicantUpdate.php">Update Profile</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="applicantReview.php">Review Application</a>
          </li>
          
        </ul>
        <form class="form-inline my-2 my-lg-0" method='post' action="../php/logout.php">
          <button class="btn btn-outline-light my-2 my-sm-0" type="button">Logout</button>
        </form>
      </div>
    </nav> <!--end of navbar-->

<?php 
include "../php/dbconfig.php";
// Establishing connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}

#FUNCTION TO COMPARE RESUME CHANGES 
#NEED TO MAKE
function read_files(){

}


  // Check if email already exists in the database
  $sql = "SELECT * FROM Applicants WHERE Email = '" . $_SESSION['Email'] . "'";

  $stmt = $conn->prepare($sql);
  if ($stmt) {
      
      // Execute the prepared statement
      if ($stmt->execute()) {
        $result = $stmt->get_result();
          
        if($result->num_rows== 1){
          while($row = $result->fetch_assoc()) {
              echo '
            <h1 style="text-align: center;"><b>Update Profile</b></h1>
    <h1 style="text-align: center;">Fill in desired fields to update below</h1>
    <form id="updateForm" method="post" action="" class="form" style="padding-left: 10px;">
        <div class="col">
            <label for="fname">First Name:</label>
            <input type="text" name="fname" placeholder="First Name" value=' . $row['FirstName'] . '>
        </div>
        <br>
        <div class="col">
            <label for="lname">Last Name:</label>
            <input type="text"  name="lname" placeholder="Last Name" value=' . $row['LastName'] . ' >
        </div>
        <br>
        <div class="col">
            <label for="email">Email:</label>
            <input type="email"  name="email" placeholder="Email" value=' . $row['Email'] . '>
        </div>
        <br>
        <div class="col">
            <label for="password">Password:</label>
            <input type="password"  name="password" placeholder="Password" value=' . $row['Password'] . '>
        </div>
        <br>
        <div class="col">
            <label for="pnumber">Phone Number:</label>
            <input type="tel"  name="pnumber" placeholder="000-000-0000" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" value=' . $row['PhoneNumber'] . '>
        </div>
        <br>
        <div class="col">
            <label for="resume">Upload Resume:</label>
            <input type="file"  name="resume" value="">    <!-- Come back to this RESUME -->
        </div>
        <br>
        <div class="center">
            <button id=button-submit class="btn btn-rounded btn-primary btn-md button-submit" type="submit">Update</button>
        </div>
    </form>';

    
   
          }

        }
        //exit();
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

  /*$check_query = 
  "UPDATE Applicants 
  SET 
    FirstName = $fname, 
    LastName= $lname, 
    Password = $password,
    Email = $email,
    PhoneNumber= $pnumber,
    ResumeFile= $resume,
  WHERE email = ?";

  $check_stmt = $conn->prepare($check_query);
  $check_stmt->bind_param("s", $email);
  $check_stmt->execute();
  $result = $check_stmt->get_result();*/
  ?>

 <!-- JavaScript code to handle form submission -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script>
    $(document).ready(function() {
        $('#updateForm').submit(function(event) {
            event.preventDefault(); // Prevent default form submission behavior

            var formData = $(this).serialize(); // Serialize form data

            // Send form data using AJAX
            $.ajax({
                type: 'POST',
                url: 'applicantUpdate2.php',
                data: formData,
                success: function(response) {
                    // Handle successful response
                    alert(response);
                    // Perform any other actions based on the response
                },
                error: function(xhr, status, error) {
                    // Handle error
                    alert('Error: ' + error);
                }
            });
        });
    });
</script>
</body>
</html>
