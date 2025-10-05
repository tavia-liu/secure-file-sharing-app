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
$SHARES_FILE = "/srv/module2group/shares.txt"; // File to store sharing information: format: owner:filename:recipient1,recipient2,...
$username = $_SESSION['username'];
$userDir= $DIR . '/' . $username;
$trashDir = $userDir . '/.trash';

// Create user and trash directory if they don't exist
if (!is_dir($userDir))  mkdir($userDir, 0755, true);
if (!is_dir($trashDir)) mkdir($trashDir, 0755, true);

// Handle messages
$msg = '';
if(isset($_GET['msg'])) {
    if ($_GET['msg'] === 'trashed') {
        $msg = "File moved to the recycle bin.";
    } elseif ($_GET['msg'] === 'restored') {
        $msg = "File restored.";
    } elseif($_GET['msg'] === 'pdeleted') {
        $msg = "File permanently deleted.";
    } elseif($_GET['msg'] === 'shared') {
        $msg = "File shared.";
    } elseif($_GET['msg'] === 'unshared') {
        $msg = "File unshared.";
    } elseif($_GET['msg'] === 'badname') {
        $msg = "Invalid file name.";
    } elseif($_GET['msg'] === 'uploaded') {
        $msg = "File uploaded.";
    } elseif($_GET['msg'] === 'no_user') {
        $msg = "This user does not exist.";
    } elseif($_GET['msg'] === 'no_file') {
        $msg = "This file does not exist.";
    } elseif ($_GET['msg'] === 'error') {
        $msg = "An error occurred. Please try again.";
    } elseif($_GET['msg'] === 'file_exists') {
        $msg = "This file already exists.";
    } elseif($_GET['msg'] === 'error_upload') {
        $msg = "File upload error.";
    }
}

// List user's file source: https://www.educative.io/answers/how-to-list-files-in-a-directory-in-php
$myFiles = [];// initialize myFiles array to store user's own files
$files = array_diff(scandir($userDir), ['.', '..', '.trash']); // remove unwanted entries: source: https://stackoverflow.com/questions/8532569/exclude-hidden-files-from-scandir
foreach ($files as $file) {
    $filePath = $userDir.'/'.$file;
    if (is_file($filePath)) {
        $myFiles[] = $file; // Add file to myFiles array
    }
}

// List shared files
$sharedFiles = [];// initialize sharedFiles array to store files shared with the user
$sharedByMe  = [];
if (file_exists($SHARES_FILE)) {
    $shares = file($SHARES_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read lines of shares information from the SHARES_FILE into an array for later looping
    foreach ($shares as $share) { // Loop each entry
        $parts = explode(':', $share);// Split by colon for later reading
        $owner = $parts[0];
        $filename = $parts[1];
        $recipients = explode(',', $parts[2]); // Split recipients by comma to get an array of usernames
        if (in_array($username, $recipients)) { // If current user is in the shared_with list
            $sharedFiles[] = ['owner' => $owner, 'filename' => $filename]; // Add to sharedFiles array: https://www.php.net/manual/en/language.types.array.php
        }
        if ($owner === $username) {
            $sharedByMe[$filename] = $recipients;
        }
    }
}



$trashFiles = [];// initialize trashFiles array to store user's trashed files
$trashed = array_diff(scandir($trashDir), ['.', '..']); // remove unwanted entries source: https://stackoverflow.com/questions/8532569/exclude-hidden-files-from-scandir
foreach ($trashed as $trash_file) {
    $filePath = $trashDir.'/'.$trash_file;
    if (is_file($filePath)) {
        $trashFiles[] = $trash_file; // Add file to trashFiles array
    }
}   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Hi, <?php echo $username; ?>!</h2>

    <!-- message box -->
    <?php if ($msg !== ''): ?>
        <div class="message"><?php echo $msg; ?></div>
    <?php endif; ?>

    <!-- Upload Form -->
     <div class="form-container">
        <h3>Upload a File</h3>
        <form action="upload.php" method="POST" enctype="multipart/form-data"> <!-- enctype source: https://www.w3schools.com/tags/att_form_enctype.asp -->
            <input type="file" name="fileToUpload" required>
            <button type="submit">Upload</button>
        </form>
    </div>
    
    <!-- My Files -->
    <div class="form-container">
        <h3>My Files</h3>
        <?php if (empty($myFiles)): ?>
             <div class="error">You have no files yet.</div>
        <?php else: ?>
            <ul>
                <?php foreach ($myFiles as $file): ?>
                    <li class="file-item">
                    <span class="filename"><?php echo $file; ?></span>

                        <!-- View button -->
                        <form method="get" action="view.php" class="function">
                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                            <button type="submit">View</button>
                        </form>

                        <!-- Delete button -->
                        <form method="get" action="trash.php" class="function">
                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                            <button type="submit" class="delete">Delete</button>
                        </form>

                        <!-- Share form -->
                        <form method="post" action="share.php" class="function share-form">
                            <label class="share-label">Share with:</label>
                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                            <input type="text" name="recipients" placeholder="user1,user2" required>
                            <button type="submit">Share</button>
                        </form>
                        <!-- Unshare form -->
                        <form method="post" action="unshare.php" class="function">
                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                            <button type="submit">Unshare</button>
                        </form>
                        <!-- shared by me-->
                        <?php if (!empty($sharedByMe[$file])): ?>
                            <span class="sharedbyme">
                            Already shared with: <?php echo implode(', ', $sharedByMe[$file]); ?>
                            </span>
                        <?php endif; ?>

                    </li>

                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Shared Files -->
    <div class="form-container">
        <h3>Files Shared With Me</h3>
        <?php if (empty($sharedFiles)): ?>
             <div class="error">No files shared with you yet.</div>
        <?php else: ?>
            <ul>
                <?php foreach ($sharedFiles as $sharedFile): ?>
                    <li class="file-item">
                        <span class="filename">
                            <?php echo $sharedFile['filename']; ?> (from <?php echo $sharedFile['owner']; ?>)
                        </span>
                        <!-- View button -->
                        <form method="get" action="view_shared.php" class="function">
                            <input type="hidden" name="file" value="<?php echo $sharedFile['filename']; ?>">
                            <input type="hidden" name="owner" value="<?php echo $sharedFile['owner']; ?>">
                            <button type="submit">View</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Trash -->
    <div class="form-container">
        <h3>Recycle Bin</h3>
        <?php if (empty($trashFiles)): ?>
             <div class="error">Recycle Bin is empty now.</div>
        <?php else: ?>
            <ul>
            <?php foreach ($trashFiles as $trash_file): ?>
                <li class="file-item">
                    <span class="filename"><?php echo $trash_file; ?></span>

                    <!-- Restore button -->
                    <form method="get" action="restore.php" class="function">
                        <input type="hidden" name="file" value="<?php echo $trash_file; ?>">
                        <button type="submit">Restore</button>
                    </form>

                    <!-- Permanently delete button -->
                    <form method="get" action="pdeleted.php" class="function">
                        <input type="hidden" name="file" value="<?php echo $trash_file; ?>">
                        <button type="submit" class="delete">Permanently Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

    <a href="logout.php">Logout</a>

</body>
</html>