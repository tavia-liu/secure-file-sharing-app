<?php
session_start();

// if user didn't login redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// define variables
$DIR = "/srv/module2group";
$username = $_SESSION['username'];
$userDir = $DIR . '/' . $username;
$trashDir= $userDir . '/.trash';
$filename = basename($_GET['file']);

$targetFile = $trashDir . '/' . $filename;


if (is_file($targetFile) && @unlink($targetFile)) { // source: https://www.php.net/manual/en/function.unlink.php
    header('Location: dashboard.php?msg=pdeleted');
    exit;
} else {
    header('Location: dashboard.php?msg=no_file');
    exit;
}