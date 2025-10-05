<?php
session_start();

// User is not logged in, redirect to login.php
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}   

// Define variables
$DIR = "/srv/module2group";
$USER_FILE = "/srv/module2group/users.txt";
$username = $_SESSION['username'];
$userDir  = $DIR . '/' . $username;
$error='';
$filename = basename($_FILES['fileToUpload']['name']);
$target_file = $userDir . '/' . $filename;

// reference for this section: https://www.w3schools.com/php/php_file_upload.asp
// Validate file name
if (!preg_match('/^[A-Za-z0-9._\- ]{1,100}$/', $filename)) {
    header('Location: dashboard.php?msg=badname');
    exit;
}

// Check if file already exists
if(file_exists($target_file)) {
    header('Location: dashboard.php?msg=file_exists');
    exit;
}
// Directory where the file will be uploaded

//Move the file to user's directory
if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
    header('Location: dashboard.php?msg=uploaded');
    exit;
} else {
    header('Location: dashboard.php?msg=error_upload');
    exit;
}
?>
