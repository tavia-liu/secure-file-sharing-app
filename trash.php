<?php
session_start();

// if user didn't login redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// define variables
$DIR       = "/srv/module2group";
$username  = $_SESSION['username'];
$userDir   = $DIR . '/' . $username;
$trashDir  = $userDir . '/.trash';
$filename  = basename($_GET['file']);
$target_file = $userDir . '/' . $filename;
$destination = $trashDir . '/' . $filename;


//make a directory for trash can if there isn't one
if (!is_dir($trashDir)) mkdir($trashDir, 0755, true);


if (is_file($target_file)) {
    if (@rename($target_file, $destination)) {
        header('Location: dashboard.php?msg=trashed');//source for rename: https://www.php.net/manual/en/function.rename.php
        exit;
    } 
} else {
    header('Location: dashboard.php?msg=no_file');
    exit;
}
