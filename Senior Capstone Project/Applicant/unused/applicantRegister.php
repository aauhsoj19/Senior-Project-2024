<?php 
include "dbconfig.php";
echo "Hello\n";

if (isset($_POST['fname'])){
$user_fname=$_POST['fname'];
}
else{
$user_fname="null";
}

if (isset($_POST['lname'])){
$user_lname=$_POST['lname'];
}
else {
$user_lname="null";
}

if (isset($_POST['email'])){
    $user_email=$_POST['email'];
}
else {
    $user_email="null";
    }

if (isset($_POST['password'])){
    $user_password=$_POST['password'];
}
else {
    $user_password="null";
    }

if (isset($_POST['pnumber'])){
    $user_pnumber=$_POST['pnumber'];
}
else {
    $user_pnumber="null";
    }    
if (isset($_POST['resume'])){
    $user_resume=$_POST['resume'];
}
else {
    $user_resume="null";
    }

$con = mysqli_connect($host, $user, $pass, $db) 
or die("<br>Cannot connect to DB:$db on $host\n");

#skipping resume for now, cuz idk how to insert images.
echo $user_fname . $user_lname . "\n$user_email";
$sql = "insert into Applicants (FirstName,LastName,Email,Password,PhoneNumber) Values ('$user_fname','$user_lname','$user_email','$user_password','$user_pnumber')";
$result = mysqli_query($con,$sql);
echo $sql;
