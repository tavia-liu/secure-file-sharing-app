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
$SHARES_FILE = "/srv/module2group/shares.txt";
$owner = $_SESSION['username'];
$ownerDir  = $DIR . '/' . $owner;
$error='';
$filename  = trim($_POST['file']);       // comes from hidden input in the form
$recipients_input = trim($_POST['recipients']); // comma-separated usernames


// reference for this section: https://www.w3schools.com/php/php_file_upload.asp
// Validate file name
if (!preg_match('/^[A-Za-z0-9._\- ]{1,100}$/', $filename)) {
    header('Location: dashboard.php?msg=badname');
    exit;
}

$recipients = [];
foreach (explode(',', $recipients_input) as $recipient) {
    $recipient = strtolower(trim($recipient));// Convert recipient to lowercase for consistent storage
    if ($recipient !== '' && !in_array($recipient, $recipients, true)) {
        $recipients[] = $recipient;
    }
}

if (empty($recipients)) {
    header('Location: dashboard.php?msg=no_user');
    exit;
}

// Validate recipients
$valid_recipients = [];
$users = file($USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($users as $user) {// Loop each user to get all valid recipients
    $parts = explode(':', $user);
    $username = strtolower(trim($parts[0]));
    if (!in_array($username, $valid_recipients, true)) $valid_recipients[] = $username;
}

$unknown = array_diff($recipients, $valid_recipients);
if (!empty($unknown)) {
    header('Location: dashboard.php?msg=no_user');
    exit;
}



$files = file($SHARES_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$kept  = [];

// copy each file to include it
foreach ($files as $file) {
    $kept[] = $file;
}

    $kept[] = $owner.':'.$filename.':'.implode(',', $recipients);


// write back 
$file_newline = implode("\n", $kept);
file_put_contents($SHARES_FILE, $file_newline, LOCK_EX);
header('Location: dashboard.php?msg=shared');
exit;
