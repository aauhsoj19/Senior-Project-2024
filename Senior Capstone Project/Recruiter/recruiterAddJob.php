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

    // Fetch form data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if required fields are filled
        $errors = array();
        if (empty($_POST['jtitle'])) {
            $errors[] = "Job Title is required.";
        }
        if (empty($_POST['jlocation'])) {
            $errors[] = "Job Location is required.";
        }
        if (empty($_POST['jdescription'])) {
            $errors[] = "Job Description is required.";
        }
        if (empty($_POST['jqualification'])) {
            $errors[] = "Job Qualification is required.";
        }
        if (empty($_POST['keywords'])) {
            $errors[] = "Keywords are required.";
        }

        // Check if at least one location option is selected
        if (empty($_POST['location'])) {
            $errors[] = "At least one job location option (Remote, Hybrid, Onsite) must be selected.";
        }

        // If there are errors, return error messages
        if (!empty($errors)) {
            $error_message = implode("<br>", $errors);
            echo json_encode(array("error" => $error_message));
            exit();
        }

        // All required fields are filled, proceed with processing the form data
        $jobTitle = $_POST['jtitle'];
        $jobLocation = $_POST['jlocation'];
        $jobDescription = $_POST['jdescription'];
        $jobQualification = $_POST['jqualification'];
        $keywords = $_POST['keywords'];
        $recruiter_id = $_SESSION['recruiter_id'];

        // Handle uploaded logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo_tmp_name = $_FILES['logo']['tmp_name'];

            // Read the file content to store in blob format
            $logo = addslashes(file_get_contents($logo_tmp_name));
        } else {
            $logo = NULL; // No logo uploaded
        }

        // Handle checkbox values for Filter column
        $filter = implode(",", $_POST['location']);

        // SQL to insert job into database
        $sql = "INSERT INTO Job (JobTitle, JobLocation, JobDescription, JobQualifications, Keywords, JobLogo, Filter, Recruiter_ID) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);
        // Bind parameters
        $stmt->bind_param("sssssssi", $jobTitle, $jobLocation, $jobDescription, $jobQualification, $keywords, $logo, $filter, $recruiter_id);

        if ($stmt->execute()) {
            echo json_encode(array("success" => "New job added successfully"));
            exit();
        } else {
            echo json_encode(array("error" => "Error: " . $sql . "<br>" . $conn->error));
            exit();
        }
    }

    $conn->close();
    ?>
