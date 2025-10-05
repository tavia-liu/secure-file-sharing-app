<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$DIR = "/srv/module2group";
$SHARES_FILE = "/srv/module2group/shares.txt";
$owner = $_SESSION['username'];
$filename = trim($_POST['file']); 


$kept = [];
$files= file($SHARES_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($files as $file) {
    $parts = explode(':', $file);
    $ownerName = $parts[0];
    $fileName  = $parts[1];
    // Keep all files that are not this owner+filename
    if (!($ownerName === $owner && $fileName === $filename)) {
        $kept[] = $file;
    }

}
file_put_contents($SHARES_FILE, implode("\n", $kept), LOCK_EX); // source: https://www.php.net/manual/en/function.file-put-contents.php
header('Location: dashboard.php?msg=unshared');
exit;
