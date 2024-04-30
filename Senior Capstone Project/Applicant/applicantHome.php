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
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only"></span></a>
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
    </nav>
  
    <!-- Search Bar and Filter -->
    <div class="container mt-3">
          <div class="row">
              <div class="col-md-6">
              <div><h1> Welcome <?php echo $_SESSION['FirstName']?>!</h1></div>
                  <form class="d-flex" method='get' action='./applicantSearch.php'>
                      <input class="form-control me-2" type="search"  name='search' placeholder="Search" aria label="Search">
                      <div class="dropdown">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="filteredDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                              Filter
                          </button>    
                          <ul class="dropdown-menu" aria-labeledby="filterDropdown">
                              <!--<li><a class="dropdown-item" href="#">Remote</a></li>
                              <li><a class="dropdown-item" href="#">Hybrid</a></li>
                              <li><a class="dropdown-item" href="#">Onsite</a></li>-->
                              
                              <input type="checkbox" id="Remote" name="filter1" value="Remote">
                              <label for="Remote">Remote</label>
                              <input type="checkbox" id="Hybrid" name="filter2" value="Hybrid">
                              <label for="Hybrid">Hybrid</label>
                              <input type="checkbox" id="on-site" name="filter3" value="on-site">
                              <label for="Onsite">Onsite</label>
                            
                          </ul>
                      </div>
                      <button class="btn btn-outline-primary" type="submit">Search</button>
                  </form>
              </div>
          </div>
      </div>

      
    
    <?php
    // Include database configuration
    include "../php/dbconfig.php";
  
    $con = mysqli_connect($host, $user, $pass, $db) 
    or die("<br>Cannot connect to DB:$dbname on $host\n");
  
    $sql="select Job_ID,JobTitle,JobDescription,JobQualifications,JobLocation,Filter,CompanyName,Recruiter_ID from CPS4951_2024S_01db.Job";
  
    $count=0;
    $jobID;
    $result = mysqli_query($con, $sql); 
  
    if($result) { 
      if (mysqli_num_rows($result) > 0) {
        
        echo '<div class="accordion" id="accordionExample">';
        echo "<h2>Avaliable Jobs:</h2><br>";
  
        while($row = mysqli_fetch_array($result)){
          $count++;
          $jobID=$row['Job_ID'];
          $jTitle=$row['JobTitle'];
          $jDesc=$row['JobDescription'];
          $jQualif=$row['JobQualifications'];
          $jLocation=$row['JobLocation'];
          $jType=$row['Filter'];
          $jCompanyName=$row['CompanyName'];
          $Recruiter_ID=$row['Recruiter_ID'];
          echo '
          <!-- Accordion -->
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading' . $count.'"> 
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$count.'" aria-expanded="true" aria-controls="collapse'. $count .'"> ' . 
                $count . '. ' . $jTitle .'
              </button>
            </h2>
            <div id="collapse'. $count.'" class="accordion-collapse collapse" aria-labelledby="heading'. $count.'">
              <div class="accordion-body">
                <strong>"Company Logo"</strong>  &nbsp; <strong>'.$jCompanyName.'</strong> <br>
                ' .$jLocation.'<br>
                ' .$jType. '<br>
                ' .$jDesc.'<br>
                ' .$jQualif.'<br>
                <p hidden>' .$jobID. ' </p>
                <p hidden>' .$Recruiter_ID. ' </p>
                
              </div>
              <button  type="submit" class="btn btn-secondary accordion-apply-btn">Apply</button>
            </div>
          </div>';
        } 
      } else {
        echo "<br>Log in doesn't exist in the database.\n";
      }
      echo '</div>';
      mysqli_free_result($result);
    } else {
      echo "<br>Something wrong with the database or SQL query.\n" ;
      echo "<br>Error message: " . mysqli_error($con);
    }
    ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
  <script>
    $(document).ready(function() {
        $('.accordion-apply-btn').click(function(event) {
            event.preventDefault(); // Prevent default button behavior

            // Capture job information
            var accordionItem = $(this).closest('.accordion-item');
            var jobTitle = accordionItem.find('.accordion-button').text().trim();
            jobTitle = accordionItem.find('.accordion-button').text().replace(jobTitle.at(0),"").trim();
            jobTitle = jobTitle.replace(jobTitle.at(0),"").trim();
            var companyName = accordionItem.find('.accordion-body').find('strong').eq(1).text().trim();
            var recruiterId = accordionItem.find('.accordion-body').find('p').eq(1).html().trim();
            var jobId = accordionItem.find('.accordion-body').find('p').eq(0).html().trim();
            

            // Create an object to hold the data
            var formData = {
                jobTitle: jobTitle,
                companyName: companyName,
                recruiterId: recruiterId,
                jobId: jobId
            };

            // Send form data using AJAX
            $.ajax({
                type: 'POST',
                url: 'applicantApply.php',
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

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
  
