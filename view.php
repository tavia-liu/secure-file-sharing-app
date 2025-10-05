<?php
session_start();

// Check if user has logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}


// Define variables
$DIR = "/srv/module2group";
$username = $_SESSION['username'];
$USER_FILE = "/srv/module2group/users.txt";
$userDir  = $DIR . '/' . $username;
$filename = basename($_GET['file']);

$fullPath = $userDir . '/' . $filename;

// Validate file name
if (!preg_match('/^[A-Za-z0-9._\- ]{1,100}$/', $filename)) {
    header('Location: dashboard.php?msg=badname');
    exit;
}

// get the mime type of the file and do inline display. source: https://www.php.net/manual/en/function.mime-content-type.php
$type = @mime_content_type($fullPath);
if ($type === false) {
    $type = 'application/octet-stream';
}
// set the headers and read the file. source: https://www.php.net/manual/en/function.readfile.php
header('Content-Type: ' . $type);
header('Content-Length: ' . filesize($fullPath));
header('Content-Disposition: inline; filename="' . basename($filename) . '"');

readfile($fullPath);
exit;