<?php
// Include database configuration
include "../php/dbconfig.php";


// Retrieve Job_ID from POST
$job_id = $_POST['job_id'];

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch accepted applicants with job details from the database
$sql = "SELECT r.Applicant_ID, r.JobTitle, r.Job_ID, r.Status  FROM Resume r INNER JOIN AppliedJobsApplicant a
ON r.Applicant_ID = a.Applicant_ID  where r.Job_ID = $job_id";

// Print out the SQL query for inspection
//echo "SQL Query: " . $sql . "<br>";

$result = $conn->query($sql);

// Check for errors
if (!$result) {
    // Output any errors
    echo "Error: " . $conn->error;
    // Exit the script to prevent further execution
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Artificial J.A.M.A</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <link href="./style.css" rel="stylesheet" type="text/css" />
    <link href="./recruiterStyle.css" rel="stylesheet" type="text/css" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light navbar-custom" style="background-color: #7C7474;">
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
</nav> <!--end of navbar-->

<div class="review-resume">
    <div class="job-title-section">
        <?php
        // Output job title and job ID fetched from the database
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '<h3>' . $row["JobTitle"] . '</h3>';
            echo '<h3>Job ID: ' . $row["Job_ID"] . '</h3>';
        } else {
            echo "<h3>Job Title: Not Available</h3>";
            echo "<h3>Job ID: Not Available</h3>";
        }
        ?>
    </div>
    <div class="applicant-section">
        <div class="accepted-section-header">
            <h4>Accepted</h4>
        </div>
        <hr>
        <?php
        // Output accepted applicants with applicant IDs fetched from the database
        if ($result->num_rows > 0) {
            do {
                echo '<div class="applicant-row">';
                echo '<div>';
                echo '<b>Applicant ID:</b> ' . $row["Applicant_ID"];
                echo '</div>';
                echo '<div class="applicant-row-buttons">';
                // Create a form to submit the Applicant ID to reviewApplicant.php
                echo '<form action="reviewApplicant.php" method="post" class="review-form">';
                echo '<input type="hidden" name="applicant_id" value="' . $row["Applicant_ID"] . '">';
                echo '<button type="submit" class="review-button">Review</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '<hr>';
            } while ($row = $result->fetch_assoc());
        } else {
            echo "<div>No accepted applicants found.</div>";
        }
        ?>
    </div>

</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

</body>
</html>

<?php
$conn->close();
?>
