<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the form data
  $teamName = $_POST['teamName'];
  $password = $_POST['password'];

  // Default skill values
  $defaultSkills = "budget=90000,time_in_sprint=380,security=0,revenue=0,stability=0,resilience=0";

  // Default overall score
  $overallScore = 0;

  // Check if the team name already exists (case-insensitive)
  $teamsData = file('teams.txt', FILE_IGNORE_NEW_LINES);
  foreach ($teamsData as $teamData) {
    $existingTeamName = strtolower(explode(',', $teamData)[0]);
    if (strtolower($teamName) === $existingTeamName) {
      // Team name already exists, set the error message and break the loop
      $errorMsg = 'Team name already exists. Please choose a different name.';
      break;
    }
  }

  if (!isset($errorMsg)) {
    // Store the team information with default skills, scores, and overall score
    $teamData = "$teamName,$password,$defaultSkills,overall_score=$overallScore" . PHP_EOL;
    file_put_contents('teams.txt', $teamData, FILE_APPEND);

    // Create a copy of baseline_stories.txt with the team name
    $sourceFile = 'baseline_stories.txt';
    $destinationFile = $teamName . '_stories.txt';
    if (copy($sourceFile, $destinationFile)) {
      // Redirect to the team page
      header('Location: teams.php');
      exit();
    } else {
      $errorMsg = 'Failed to create team stories file. Please try again.';
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>ZeroTrust Game - Create Team</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    .error-box {
      background-color: #f8d7da;
      color: #721c24;
      padding: 10px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h3 class="text-center">Create Team</h3>
          </div>
          <div class="card-body">
            <?php if (isset($errorMsg)) : ?>
              <div class="error-box">
                <?php echo $errorMsg; ?>
              </div>
            <?php endif; ?>

            <form action="register.php" method="post">
              <div class="form-group">
                <label for="teamName">Team Name:</label>
                <input type="text" class="form-control" id="teamName" name="teamName" required>
              </div>
              <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary">Create Team</button>
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
