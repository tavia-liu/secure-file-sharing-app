<?php
session_start();
$USER_FILE = "/srv/module2group/users.txt";// Path to the user data file
$errors = [];// Initialize an array for error messages
$success = "" ;// Flag to indicate if the password reset was successful
$username = "";
$email = "";
$birthdate = "";
$birthcity = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $birthdate = trim($_POST['birthdate']);
    $birthcity = trim($_POST['birthcity']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];// Initialize variables for input values


    // Validate inputs
    if ($username === '' || $email === '' || $new_password === '' || $confirm_password === '' || $birthdate === '' || $birthcity === '') {
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

    if ($new_password !== '' && strlen($new_password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }

    if ($new_password !== '' && $confirm_password !== '' && $new_password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }


    // If no errors, proceed to reset password
    if (empty($errors) && file_exists($USER_FILE)) {
        $u_lower = strtolower($username);// Convert username and email to lowercase for consistency
        $e_lower = strtolower($email);
        $user_found = false;
        $user_data = file($USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read users from file into an array
        $updated_data = [];//initialize an array to hold updated user data with new password
        foreach ($user_data as $user) {// Loop each user to find a match
            $parts = explode(':', $user);// Split user data by colon for later reading
            $username_stored = $parts[0];
            $email_stored = $parts[1];
            $hashed_password_stored = $parts[2];
            $hashed_birthdate_stored = $parts[3]; 
            $hashed_birthcity_stored = $parts[4]; 
            if ($username_stored === $u_lower && $email_stored === $e_lower) {
                // Check security Questions
                $birthcity_norm = mb_strtolower($birthcity, 'UTF-8');// Normalize birthcity to lowercase for comparison
                if (password_verify($birthdate, $hashed_birthdate_stored) && password_verify($birthcity_norm, $hashed_birthcity_stored)) {
                    $user_found = true;
                    // if passed checks, update password
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $updated_data[] = $username_stored.':'.$email_stored.':'.$new_hash.':'.$hashed_birthdate_stored.':'.$hashed_birthcity_stored;
                    $success = "Password reset successful! You can now <a href='login.php'>log in</a> with your new password.";
                } else {
                    $errors[] = "Security answers do not match!";
                    $updated_data[] = $user; // keep old user's data
                }
            } else {
                $updated_data[] = $user;
            }
        }
        if ($user_found && empty($errors)) {
                file_put_contents($USER_FILE, implode("\n", $updated_data), LOCK_EX);// Upload updated user data by gluing them together into one string and store back to the file with newline in between them: source: https://stackoverflow.com/questions/1480617/php-implode-n-apparray-generates-extra
            }
        if (!$user_found && empty($errors)) {
                $errors[] = "User not found. Please <a href='register.php'>register</a> first.";
            }
        
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <h2>Reset Your Password Here!</h2>

  <form method="post" action="forgot_password.php" class="form-container">
    <label for="username">Username</label>
    <input
      id="username"
      name="username"
      type="text"
      required
      value="<?php echo $username; ?>"
    >
    <label for="email">Email</label>
    <input
      id="email"
      name="email"
      type="email"
      required
      value="<?php echo $email; ?>"
    >
    <label for="birthdate">Birthdate (YYYY-MM-DD)</label>
    <input id="birthdate" name="birthdate" type="text" required value="<?php echo $birthdate; ?>">

    <label for="birthcity">Birth City</label>
    <input id="birthcity" name="birthcity" type="text" required value="<?php echo $birthcity; ?>">

    <label for="new_password">New Password</label>
    <input id="new_password" name="new_password" type="password" required>

    <label for="confirm_password">Confirm New Password</label>
    <input id="confirm_password" name="confirm_password" type="password" required>

    <button type="submit">Reset Password</button>

    <?php if (!empty($errors)) { ?>
      <div class="error">
        <ul>
          <?php foreach ($errors as $e) { echo "<li>$e</li>"; } ?>
        </ul>
      </div>
    <?php } ?>

    <?php if ($success !== "") { ?>
      <div class="error"><?php echo $success; ?></div>
    <?php } ?>
  </form>

  <p><a href="login.php">Go back to login</a></p>
  <p><a href="register.php">Need an account? Register here!</a></p>

</body>
</html>