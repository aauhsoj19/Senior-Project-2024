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

// Check if Job_ID is provided via POST
if (!isset($_POST['job_id'])) {
    // Redirect back to recruiterViewJobs.php or display an error
    header("Location: recruiterViewJobs.php");
    exit();
}

// Retrieve Job_ID from POST
$job_id = $_POST['job_id'];

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch job title based on the provided Job_ID
$sql_job_title = "SELECT JobTitle FROM Job WHERE Job_ID = $job_id";
$result_job_title = $conn->query($sql_job_title);

if ($result_job_title->num_rows > 0) {
    $row_job_title = $result_job_title->fetch_assoc();
    $job_title = $row_job_title['JobTitle'];
} else {
    $job_title = "Job Title Not Found";
}

// Fetch applicant IDs and concatenated names based on the provided Job_ID
//$sql = "SELECT AppliedJobs_ID, Applicant_ID, Job_ID, CONCAT(ApplicantName, ' ', LastName) AS FullName FROM AppliedJobsApplicant WHERE Job_ID = $job_id";
$sql = "SELECT AppliedJobs_ID, Applicant_ID, Job_ID, CONCAT(ApplicantName, ' ', LastName) AS FullName 
        FROM AppliedJobsApplicant 
        WHERE Job_ID = $job_id AND Status = 'In Progress'";

$result = $conn->query($sql);
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
    <!--<link href="reviewApplicant.css" rel="stylesheet" type="text/css"/>-->


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

<div class="review-resume">
    <div class="job-title-section">
        <h3><?php echo $job_title; ?></h3>
        <h3>Job ID: <?php echo $job_id; ?></h3>
    </div>
    <div class="applicant-section">
        <div class="applicant-section-header">
            <div>
                <h4>Applicants</h4>
            </div>
        </div>
        <hr>
        <?php
        // Output applicant IDs and names fetched from the database
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="applicant-row">';
                echo '<div>Applicant ID: ' . $row["Applicant_ID"] . '</div>';
                echo '<div class="applicant-row-buttons">';
                //Review button with form
                echo '<form action="reviewApplicant.php" method="post" class="review-form">';
                echo '<input type="hidden" name="applicant_id" value="' . $row["Applicant_ID"] . '">';
                echo '<button type="submit" class="review-button">Review</button>';
                echo '</form>';
                // Accept button with form
                echo '<form action="acceptApplication.php" method="post">';
                echo '<input type="hidden" name="applied_id" value="' . $row["AppliedJobs_ID"] . '">';
                echo '<input type="hidden" name="applicant_id" value="' . $row["Applicant_ID"] . '">';
                echo '<input type="hidden" name="job_id" value="' . $job_id . '">';
                echo '<button type="submit" class="accept-button">Accept</button>';
                echo '</form>';
                // Reject button with form
                echo '<form action="rejectApplication.php" method="post">';
                echo '<input type="hidden" name="applied_id" value="' . $row["AppliedJobs_ID"] . '">';
                echo '<button type="submit" class="reject-button">Reject</button>';
                echo '</form>';

                echo '</div>';
                echo '</div>';
                echo '<hr>';
            }
        } else {
            echo "<div>No applicants found for this job.</div>";
        }
        ?>

    </div>
    <div class="view-accepted-applicants">
    <form action="recruiterReviewAcceptedApplicant.php" method="post">
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
        <button type="submit" class="view-accepted-applicants-button">View Accepted Applicants</button>
    </form>
</div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

</body>
</html>
