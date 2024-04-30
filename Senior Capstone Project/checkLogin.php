<?php
error_reporting(E_ALL);

session_start();
// Include database configuration file
include "./php/dbconfig.php";

try {
    $input_email = isset($_POST['email']) ? $_POST['email'] : '';
    $input_password = isset($_POST['password']) ? $_POST['password'] : '';
    $input_user = isset($_POST['user']) ? $_POST['user'] : '';

    $con = mysqli_connect($host, $user, $pass, $db);
    if (!$con) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    checkLogin($con, $input_email, $input_password, $input_user);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

function checkLogin($con, $input_email, $input_password, $input_user) {
    if ($input_user == 'Applicant') {
        $table = 'Applicants';
        $path = './Applicant/applicantHome.php';
        // Using prepared statements to prevent SQL injection
        $stmt = $con->prepare("SELECT Password, FirstName FROM $table WHERE email = ?");
        $stmt->bind_param("s", $input_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['Password']; // Unhashed password retrieved from the database
            if ($input_password == $stored_password) {
                echo "Welcome " . $row['FirstName'];
                $_SESSION['FirstName'] = $row['FirstName'];
                $_SESSION['Email'] = $input_email;
                $_SESSION['logged_in'] = true;
                header('Location: ' . $path);
                exit();
            } else {
                echo "Incorrect password<br>";
            }

        } else {
            echo "User doesn't exist. 1 <br>";
        }

        $stmt->close();
        mysqli_close($con);
    } elseif ($input_user == 'Recruiter') {
        $table = 'Recruiters';
        $path = './Recruiter/recruiterHome.php';

        // Using prepared statements to prevent SQL injection
        $stmt = $con->prepare("SELECT Recruiter_ID, Password, FirstName FROM $table WHERE email = ?");
        $stmt->bind_param("s", $input_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['Password']; // Unhashed password retrieved from the database
            if ($input_password == $stored_password) {
                echo "Welcome " . $row['FirstName'];
                $_SESSION['Email'] = $input_email;
                $_SESSION['logged_in'] = true;
                // Set Recruiter_ID in session
                $_SESSION['recruiter_id'] = $row['Recruiter_ID'];
                // Redirect based on user type
                header('Location: ' . $path);
                exit();
            } else {
                echo "Incorrect password<br>";
            }
        } else {
            echo "User doesn't exist 2.<br>";
        }
        $stmt->close();
        mysqli_close($con);
    } else {
        echo "Invalid User type";
        exit();
    }
}
?>
