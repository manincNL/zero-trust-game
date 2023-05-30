<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

// Check if the user is an admin (optional)
// You can modify this condition as per your admin logic
if ($_SESSION['username'] !== 'admin') {
  die('Access denied.'); // Display an error message or handle the case as desired
}

// Read team data from teams.txt file
$teamsData = file('teams.txt', FILE_IGNORE_NEW_LINES);

// Function to update the teams.txt file
function updateTeamsFile($teamsData) {
  file_put_contents('teams.txt', implode("\n", $teamsData));
}

// Function to remove a team from the teams.txt file and delete its stories file
function removeTeam($teamName, &$teamsData) {
  foreach ($teamsData as $index => $teamData) {
    $teamInfo = explode(',', $teamData);
    $existingTeamName = $teamInfo[0];

    if ($existingTeamName === $teamName) {
      unset($teamsData[$index]);
      updateTeamsFile($teamsData);

      // Delete the team's stories file
      $teamStoriesFile = $existingTeamName . '_stories.txt';
      if (file_exists($teamStoriesFile)) {
        unlink($teamStoriesFile);
      }

      return true;
    }
  }

  return false;
}

// Handle form submission to update team name or password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $teamName = $_POST['team_name'];

  // Check if the team exists
  $teamExists = false;
  foreach ($teamsData as $index => $teamData) {
    $teamInfo = explode(',', $teamData);
    $existingTeamName = $teamInfo[0];

    if ($existingTeamName === $teamName) {
      $teamExists = true;

      // Update team name and password
      $teamsData[$index] = $_POST['new_team_name'] . ',' . $_POST['new_team_password'];
      updateTeamsFile($teamsData);

      break;
    }
  }

  if (!$teamExists) {
    // Display an error message or handle the case when the team does not exist
  }
}

// Handle form submission to remove a team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_team'])) {
  $teamName = $_POST['team_name'];

  // Remove the team from the teams.txt file and delete its stories file
  $removed = removeTeam($teamName, $teamsData);

  if (!$removed) {
    // Display an error message or handle the case when the team does not exist
  }
}

// Handle form submission to remove all teams
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_all_teams'])) {
  // Remove all teams from the teams.txt file
  $teamsData = [];
  updateTeamsFile($teamsData);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>ZeroTrust Game - Admin</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">ZeroTrust Game</a>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item active"><a class="nav-link" href="admin.php">Admin</a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
    </ul>
  </nav>
  
  <div class="container mt-5">
    <h1>Admin Panel</h1>

    <h2>Edit Team</h2>
    <form action="admin.php" method="post">
      <div class="form-group">
        <label for="team_name">Team Name:</label>
        <input type="text" class="form-control" id="team_name" name="team_name" required>
      </div>
      <div class="form-group">
        <label for="new_team_name">New Team Name:</label>
        <input type="text" class="form-control" id="new_team_name" name="new_team_name" required>
      </div>
      <div class="form-group">
        <label for="new_team_password">New Team Password:</label>
        <input type="password" class="form-control" id="new_team_password" name="new_team_password" required>
      </div>
      <button type="submit" class="btn btn-primary">Update Team</button>
    </form>

    <h2>Remove Team</h2>
    <form action="admin.php" method="post">
      <div class="form-group">
        <label for="team_name">Team Name:</label>
        <input type="text" class="form-control" id="team_name" name="team_name" required>
      </div>
      <button type="submit" class="btn btn-danger" name="remove_team">Remove Team</button>
    </form>

    <h2>Remove All Teams</h2>
    <form action="admin.php" method="post">
      <button type="submit" class="btn btn-danger" name="remove_all_teams">Remove All Teams</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
