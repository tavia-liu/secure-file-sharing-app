<?php
session_start();

$USER_FILE = "/srv/module2group/users.txt";// Path to the user data file
$errors = [];// Initialize an array for error messages
$username = '';// Initialize variables for input values
$email = '';
$password = '';

// User is already logged in, redirect to dashboard.php
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the form is submitted
    $username = trim($_POST['username']); // Remove whitespace by using trim()
    $email = trim($_POST['email']);
    $password = $_POST['password'];

  if($username === '' || $email === '' || $password === '') {// Validate inputs
    $errors[] = "All fields are required!";
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email format
    $errors[] = "Invalid email address!";
  }


//proceed to checking the user credentials
if(empty($errors)) {
  if(file_exists($USER_FILE)) {
    $u_lower = strtolower($username);// Convert username and email to lowercase for consistency
    $e_lower = strtolower($email);
    $user_found = false;
    $user_data = file($USER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read users from file into an array
    foreach ($user_data as $user) {// Loop each user to find a match
      $parts = explode(':', $user);// Split user data by colon for later reading
      $username_ = $parts[0];
      $email_ = $parts[1];
      $hashed_password = $parts[2];
      if ($username_ === $u_lower && $email_ === $e_lower){ // compare username and email in the file
          $user_found = true;
          if (password_verify($password, $hashed_password)) { // Verify the password input by teh user against the hashed password in the file
              $_SESSION['username'] = $u_lower; 
              $_SESSION['email'] = $e_lower;// If password is correct, log in the user
              header('Location: dashboard.php'); // Redirect to dashboard.php after successful login
              exit;
          } else {
              $errors[] = "Incorrect password.";
          }
          break;
      }
      
    }
    if (!$user_found && empty($errors)) {
              $errors[] = "Username or email not found. Create an account first.";
    }
  } else {
      $errors[] = "User file does not exist. ";
    }
}
}
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Welcome To FileShare!</h2>
  <br>
  <h2>Login</h2>

  <form method="post" action="login.php" class="form-container">
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
    <label for="password">Password</label>
    <input
      id="password"
      name="password"
      type="password"
      required
    >
    <button type="submit">Login</button>
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
        <a href="register.php">Don't have an account? Register Here!</a>
    </p>
    <p>
        <a href="forgot_password.php">Forgot Password? Reset it Here!</a>
    </p>


</body>
</html>    