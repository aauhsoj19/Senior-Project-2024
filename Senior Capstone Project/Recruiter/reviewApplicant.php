<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Review Applicant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="./style.css" rel="stylesheet" type="text/css" />
    <link href="./recruiterStyle.css" rel="stylesheet" type="text/css" />
    <!--<link href="reviewApplicant.css" rel="stylesheet" type="text/css"/>-->
</head>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom" style="background-color: #7C7474;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img class="custom-logo" src="../assets/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="recruiterHome.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="recruiterViewJobs.php">View Jobs</a>
                    </li>
                </ul>
                <form class="d-flex">
                    <button class="btn btn-outline-light" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav> <!--end of navbar-->
<main>
    <div class="review-resume">
        <div class="applicant-info_title">
            <h2>Applicant Information</h2>
        </div>
        <div class="applicant-section">
            <?php
            // Include database configuration
            include "../php/dbconfig.php";

            // Create connection
            $conn = new mysqli($host, $user, $pass, $db);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if Applicant_ID is set
            if (isset($_POST['applicant_id'])) {
                // Sanitize the input to prevent SQL injection
                $applicant_id = $conn->real_escape_string($_POST['applicant_id']);

                // Query to fetch applicant information from Applicants table
                $sql = "SELECT CONCAT(FirstName, ' ', LastName) AS Name, Email, PhoneNumber, ResumeFile
                        FROM Applicants
                        WHERE Applicant_ID = $applicant_id";
                $result = $conn->query($sql);

                // Check if query was successful
                if ($result) {
                    // Fetch applicant information
                    $row = $result->fetch_assoc();

                    // Display applicant information
                    $name = $row['Name'];
                    $email = $row['Email'];
                    $phone_number = $row['PhoneNumber'];
                    $resume_file = $row['ResumeFile'];

                    // Display the applicant information in the HTML
                    echo '<div class="applicant-section-header">';
                    echo "<h4>Applicant ID: $applicant_id</h4>";
                    echo '</div>';
                    echo '<hr>';
                    echo '<div class="applicant-details">';
                    echo "<p><b>Name:</b> $name</p>";
                    echo "<p><b>Email:</b> $email</p>";
                    echo "<p><b>Phone Number:</b> $phone_number</p>";
                    echo '<p class="resume">';
                    echo "<span><b>Resume:</b> $resume_file</span>";
                    // Download button for the resume
                    echo '<form action="../path/to/resumes/'.$resume_file.'" method="get">';
                    echo '<button type="submit" class="resume">Download Resume</button>';
                    echo '</form>';
                    echo '</p>';
                    echo '</div>';
                } else {
                    // If query fails, display an error message
                    echo "Error fetching applicant information: " . $conn->error;
                }
            } else {
                // If Applicant_ID is not set, redirect to the previous page or handle accordingly
                header("Location: recruiterReviewAcceptedApplicant.php");
                exit();
            }
            ?>
        </div>
        <div class="d-flex justify-content-center align-items-center">
            <div class="go-back-review-applicant-but">
                <button class="btn btn-primary go-back-review-applicant-button" onclick="history.back()">Go back Review Applicant</button>
            </div>
        </div>
    </div>
</main>

<footer>
    <b>Copyright &copy; 2024 Joshua Roasa, Molisha Khosla, Anya Carr, Adriana
        Altiamirano. All Rights Reserved.</b>
</footer>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>
</html>
