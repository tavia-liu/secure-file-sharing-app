<?php
session_start();

// User is not logged in, redirect to login.php
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}   
// Define variables
$DIR = "/srv/module2group";
$username  = $_SESSION['username'];
$userDir= $DIR . '/' . $username;
$trashDir= $userDir . '/.trash';
$filename= basename($_GET['file']);
$destination = $userDir . '/' . $filename;
$target_file = $trashDir . '/' . $filename;


if (is_file($target_file)) {
    if (@rename($target_file, $destination)) {
        header('Location: dashboard.php?msg=trashed');//source for rename: https://www.php.net/manual/en/function.rename.php
        exit;
    } 
} else {
    header('Location: dashboard.php?msg=no_file');
    exit;
}
