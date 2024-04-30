<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Artificial J.A.M.A</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <link href="./style.css" rel="stylesheet" type="text/css"/>
    <link href="recruiterStyle.css" rel="stylesheet" type="text/css"/>
   <!-- <link href="recruiterviewJobs.css" rel="stylesheet" type="text/css"/>-->

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light navbar-custom"
     style="background-color: #7C7474;">
    <a class="navbar-brand" href="#">
        <img class="custom-logo" src="../assets/logo.png" alt="Logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="recruiterHome.php">Home <span
                            class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="recruiterViewJobs.php">View Jobs</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0" method="post" action="../php/logout.php">
            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Logout</button>
        </form>
    </div>
</nav>

<div class="view-jobs">
    <form action="recruiterAddJob.html">
        <div class="add-job-button">
            <button type="submit">Add Job</button>
        </div>
    </form>
    <?php
    // Include database configuration
    include "../php/dbconfig.php";

    // Start session (if not already started)
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['recruiter_id'])) {
        // Redirect to login page or handle unauthorized access
        header("Location: checkLogin.php");
        exit();
    }

    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch job titles based on Recruiter_ID from session
    $recruiter_id = $_SESSION['recruiter_id'];
    $sql = "SELECT Job_ID, JobTitle FROM Job WHERE Recruiter_ID = $recruiter_id";
    $result = $conn->query($sql);

    // Output job titles fetched from the database
    if ($result->num_rows > 0) {
        $counter = 1; // Initialize counter for job numbering
        while ($row = $result->fetch_assoc()) {
            echo '<div class="view-jobs-row">';
            // Display the job number
            echo '<p class="job-title">'. $counter,'. ' . $row["JobTitle"] . '</p>';
            echo '<div class="button-div">';
            
            // Review button
            echo '<form action="recruiterReviewApplicant.php" method="post" class="review-form">';
            echo '<input type="hidden" name="job_id" value="' . $row["Job_ID"] . '">';
            echo '<button type="submit" class="review-button">Review</button>';
            echo '</form>';
            
            // Update button
            echo '<form action="recruiterUpdateJob.php" method="post">';
            echo '<input type="hidden" name="job_id" value="' . $row["Job_ID"] . '">';
            echo '<button type="submit" class="update-button">Update</button>';
            echo '</form>';
            
            // Close button
            echo '<form onsubmit="return confirmClose();" method="post" action="recruiterCloseJob.php">'; 
            echo '<input type="hidden" name="job_id" value="' . $row["Job_ID"] . '">';
            echo '<button type="submit" name="close_job" class="close-button">Close</button>';
            echo '</form>';
            
            echo '</div>';
            echo '</div>';

            // Increment counter for the next iteration
            $counter++;
        }
    } else {
        echo "<div class='no-jobs-found' style='color: white;'>No jobs found</div>";
    }

    $conn->close();
    ?>

    <script>
        function confirmClose() {
            return confirm('Are you sure you want to close this job?');
        }
    </script>
</div>

</body>
</html>
