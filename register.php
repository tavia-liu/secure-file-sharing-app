<?php
session_start();
$USER_FILE = "/srv/module2group/users.txt";
$DIR = "/srv/module2group";
$errors = [];


// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the form is submitted
    $username = trim($_POST['username']); // Remove whitespace by using trim()
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $birthdate = trim($_POST['birthdate']);
    $birthcity = trim($_POST['birthcity']);

    // Validate inputs
    if ($username === '' || $email === '' || $password === '' || $confirm_password === '' || $birthdate === '' || $birthcity === '') {
        $errors[] = "All fields are required!";
    }
    if ($birthdate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
        $errors[] = "Birthday must be in this format: YYYY-MM-DD";
    }

    if ($username !== '' && !preg_match('/^[A-Za-z0-9]{4,24}$/', $username)) {
        $errors[] = "Username must be 4-24 characters and contain only letters and numbers.";
    }

     if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address!";
    }

    if ($password !== '' && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }
    if ($password !== '' && $confirm_password !== '' && $password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }



    
    // Check if username or email already exists
    if (empty($errors) && file_exists($USER_FILE)) {
        $users = file($USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read users from file into an array
        foreach ($users as $user) {// Loop each user
            $parts = explode(':', $user);// Split user data by colon for later reading
            $username_ = strtolower(trim($parts[0]));
            $email_ = strtolower(trim($parts[1]));
            if ($username_ === strtolower($username)) {
            $errors[] = "Username is already taken.";
            break;
            }
            if ($email_ === strtolower($email)) {
            $errors[] = "Email address is already registered.";
            break;
            }
        }
    }

   
    
    // If no errors, register the user
    if (empty($errors)) {
        $username_lc = strtolower($username); // Convert username and email to lowercase for consistent storage
        $email_lc    = strtolower($email);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security:https://www.php.net/manual/en/function.password-hash.php
        $birthcity_norm = mb_strtolower($birthcity, 'UTF-8'); // Normalize birthcity to lowercase for consistent storage
        $hashed_birthcity = password_hash($birthcity_norm, PASSWORD_DEFAULT); // Hash the birthcity for security
        $hashed_birthdate = password_hash($birthdate, PASSWORD_DEFAULT); // Hash the birthdate for security
        $user_data = "$usernasme_lc:$email_lc:$hashed_password:$hashed_birthdate:$hashed_birthcity\n"; // create user data string for later storing
        $userDir = $DIR . '/' . $username_lc; // Create user directory path
        if (!is_dir($userDir)) { // Check if user directory already exists
            mkdir($userDir, 0755, true); // Create user directory with permissions 0755: https://www.php.net/manual/en/function.mkdir.php
        }
        file_put_contents($USER_FILE, $user_data, FILE_APPEND | LOCK_EX); // Append user data to the users file: https://www.php.net/manual/en/function.file-put-contents.php
        header('Location: login.php'); // Redirect to login page after the registration
        exit;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Create Your Account</h2>
    <form method ="POST" action="register.php" class="form-container">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required pattern="[A-Za-z0-9]{4,24}" title="4-24 characters, letters and numbers only"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="6"><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"><br>

        <label for="birthdate">Birthdate (YYYY-MM-DD):</label>
        <input type="text" id="birthdate" name="birthdate" required pattern="\d{4}-\d{2}-\d{2}" title="Format: YYYY-MM-DD"><br>

        <label for="birthcity">Birth City:</label>
        <input type="text" id="birthcity" name="birthcity" required><br>

        <button type="submit">Register</button>
        <?php if (!empty($errors)) { ?>
            <div class="error">
                <ul>
                    <?php
                    foreach ($errors as $error) {
                        echo $error . "<br>";
                    }
                    ?>
                </ul>
            </div>
        <?php } ?>
    </form>
    <p>
        <a href="login.php">Already have an account? Log in Here!</a>
    </p>

</body>
</html>    
        