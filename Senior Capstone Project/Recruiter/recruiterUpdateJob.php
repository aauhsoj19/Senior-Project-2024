<?php
// Include database configuration
include "../php/dbconfig.php";

// Start session (if not already started)
session_start();

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if user is logged in
    if (!isset($_SESSION['recruiter_id'])) {
        // Redirect to login page or handle unauthorized access
        header("Location: checkLogin.php");
        exit();
    }

    // Fetch form data
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id'])) {
        // Fetch job details based on job ID
        $job_id = $_POST['job_id'];
        $sql = "SELECT * FROM Job WHERE Job_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch job details
            $row = $result->fetch_assoc();
            $jobTitle = $row['JobTitle'];
            $jobLocation = $row['JobLocation'];
            $jobDescription = $row['JobDescription'];
            $jobQualification = $row['JobQualifications'];
            $keywords = $row['Keywords'];
            $logo = $row['JobLogo'];
            $filter = explode(",", $row['Filter']); // Convert comma-separated locations to array
        } else {
            // No job found with the provided ID
            throw new Exception("No job found with the provided ID.");
        }
    } else {
        // No job ID provided
        throw new Exception("No job ID provided.");
    }
} catch (Exception $e) {
    // Handle exceptions here
    echo "Error: " . $e->getMessage();
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
    <link href="../style.css" rel="stylesheet" type="text/css"/>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.logo').addEventListener('change', function() {
                var fileName = this.files[0].name;
                var fileInputLabel = document.querySelector('.file-input-label');
                if (fileInputLabel) {
                    fileInputLabel.innerText = fileName;
                }
            });

            document.querySelector('.form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                var form = this;
                var formData = new FormData(form); // Create FormData object to send form data

                // Send form data via AJAX
                fetch('recruiterUpdateJobAction.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Parse JSON response
                .then(data => {
                    // Handle response
                    if (data.error) {
                        // Display error message
                        alert(data.error);
                    } else if (data.success) {
                        // Display success message as an alert
                        alert(data.success);
                        // Reload the page after successful update
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Populate form fields with fetched data
            document.getElementById('jtitle').value = "<?php echo $jobTitle; ?>";
            document.getElementById('jlocation').value = "<?php echo $jobLocation; ?>";
            document.getElementById('jdescription').value = "<?php echo $jobDescription; ?>";
            document.getElementById('jqualification').value = "<?php echo $jobQualification; ?>";
            document.getElementById('keywords').value = "<?php echo $keywords; ?>";
            // Handle checkbox values
            var locations = <?php echo json_encode($filter); ?>;
            locations.forEach(location => {
                document.getElementById(location.toLowerCase()).checked = true;
            });
        });
    </script>
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
                <a class="nav-link" href="recruiterHome.html">Home <span
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
</nav>  <!--End of Navbar-->

<div class="job-container">
    <form method="post" action="recruiterUpdateJobAction.php" class="form" enctype="multipart/form-data">
        <!-- Populate form fields with fetched data -->
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
        <h3 style="text-align: center;">Please fill each required field below to update job.</h3>
        <br>
        <div class="field flex">
            <label for="logo">Company Logo</label>
            <label class="file-input-label">
                <input class="logo" type="file" name="logo" accept="image/*">
                Choose File
            </label>
        </div>
        <br>
        <div class="field flex">
            <label for="jtitle">Job Title</label>
            <input type="text" id="jtitle" name="jtitle" placeholder="Job Title" required>
        </div>
        <br>
        <div class="field flex">
            <label for="jlocation">Job Location</label>
            <input type="text" id="jlocation" name="jlocation" placeholder="Location" required>
        </div>
        <br>
        <div class="field flex">
            <label for="remote">Remote</label>
            <div class="flex remote">
                <input type="checkbox" id="remote" name="location[]" value="Remote">
                <label for="remote">Remote</label>
                <input type="checkbox" id="hybrid" name="location[]" value="Hybrid">
                <label for="hybrid">Hybrid</label>
                <input type="checkbox" id="onsite" name="location[]" value="Onsite">
                <label for="onsite">Onsite</label>
            </div>
        </div>
        <br>
        <div class="field flex">
            <label for="jdescription">Job Description</label>
            <textarea type="text" id="jdescription" name="jdescription" placeholder="Description"
                      class="text-area resizable-input" cols="50" contenteditable="true"
                      required></textarea>
        </div>
        <br>
        <div class="field flex">
            <label for="jqualification">Job Qualification</label>
            <textarea type="text" id="jqualification" name="jqualification" placeholder="Qualification"
                      class="text-area resizable-input" cols="50" contenteditable="true"
                      required></textarea>
        </div>
        <br>
        <div class="field flex">
            <label for="keywords">Keywords</label>
            <input type="text" id="keywords" name="keywords" placeholder="Keywords" required>
        </div>
        <br>
        <div class="center">
            <button class="btn btn-rounded btn-primary btn-md button-job-submit" type="submit">Update Job</button>
        </div>
    </form>
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
