<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the form data
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Check if the username and password match the admin credentials
  include 'admin_password.php';

  if ($username === $adminUsername && $password === $adminPassword) {
    // Start the session and store the username
    session_start();
    $_SESSION['username'] = $username;

    // Redirect to the beheer page (admin dashboard)
    header('Location: beheer.php');
    exit();
  } else {
    // Read team data from teams.txt file
    $teamsData = file('teams.txt', FILE_IGNORE_NEW_LINES);

    // Check if the username and password match any team
    $authenticated = false;
    foreach ($teamsData as $teamData) {
      $teamInfo = explode(',', $teamData);
      $teamName = $teamInfo[0];
      $teamPassword = $teamInfo[1];

      if ($teamName === $username && $teamPassword === $password) {
        // Authentication successful
        $authenticated = true;
        break;
      }
    }

    if ($authenticated) {
      // Start the session and store the username
      session_start();
      $_SESSION['username'] = $username;

      // Redirect to the team page
      header('Location: teams.php');
      exit();
    } else {
      // Incorrect username or password
      $errorMessage = "Username or password not found. Please try again.";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>ZeroTrust Game - Login</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="text-center">Login</h3>
          </div>
          <div class="card-body">
            <?php
              // Display error message if exists
              if (isset($errorMessage)) {
                echo '<div class="alert alert-danger">' . $errorMessage . '</div>';
              }
            ?>
            <form action="login.php" method="post">
              <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
              <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
              <p class="text-center mt-3">Don't have an account? <a href="register.php">Create Account/Team</a></p>
              <div class="text-center mt-3">
                <a href="index.php" class="btn btn-secondary">Home</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
