<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

// Get the current username
$username = $_SESSION['username'];

// Read team data from teams.txt file
$teamsData = file('teams.txt', FILE_IGNORE_NEW_LINES);

// Find the team entry for the current user
$teamIndex = -1;
foreach ($teamsData as $index => $teamData) {
  $teamInfo = explode(',', $teamData);
  $teamName = $teamInfo[0];

  if ($teamName === $username) {
    $teamIndex = $index;
    break;
  }
}

// Check if the team entry was found
if ($teamIndex === -1) {
  die('Team not found.'); // Display an error message or handle the case as desired
}

// Get the current team information
$currentTeamInfo = explode(',', $teamsData[$teamIndex]);
$currentTeamName = $currentTeamInfo[0];
$currentTeamPassword = $currentTeamInfo[1];

// Get the current team skills and overall score
$currentSkills = array_slice($currentTeamInfo, 2, 7); // Assuming the skills are in positions 2 to 8
$overallScore = explode('=', $currentTeamInfo[9])[1]; // Assuming overall score is in position 9

// Handle form submission to change team name
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newTeamName = $_POST['new_team_name'];

  // Update the team name in the teams.txt file
  $teamsData[$teamIndex] = $newTeamName . ',' . $currentTeamPassword . implode(',', $currentSkills) . ',overall_score=' . $overallScore;
  file_put_contents('teams.txt', implode("\n", $teamsData));

  // Update the current username in the session
  $_SESSION['username'] = $newTeamName;

  // Redirect back to the teams page
  header('Location: teams.php');
  exit();
}

// Function to read user stories from the team's stories file
function getUserStories($teamName) {
  $storiesFile = $teamName . '_stories.txt';

  if (file_exists($storiesFile)) {
    $storiesData = file($storiesFile, FILE_IGNORE_NEW_LINES);

    $userStories = [];
    foreach ($storiesData as $storyData) {
      $storyInfo = explode(',', $storyData);
	  $storyid = $storyInfo[0];
      $description = $storyInfo[1];
      $complexity = $storyInfo[2];
      $revenue = $storyInfo[3];
      $time = $storyInfo[4];
      $completed = $storyInfo[9];

      $userStories[] = [
	    'storyid' => $storyid,
        'description' => $description,
        'complexity' => $complexity,
        'revenue' => $revenue,
        'time' => $time,
        'completed' => $completed
      ];
    }

    // Sort the user stories based on completed status in ascending order
    usort($userStories, function ($a, $b) {
      return strcmp($a['completed'], $b['completed']);
    });

    return $userStories;
  }

  return [];
}

// Get user stories for the current team
$userStories = getUserStories($currentTeamName);
?>

<!DOCTYPE html>
<html>
<head>
  <title>ZeroTrust Game - Team Area</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    .card-container {
      display: flex;
      flex-wrap: wrap;
    }
    .card {
      width: 18rem;
      margin: 10px;
      position: relative;
    }
    .card-body {
      padding: 1rem;
    }
    .completed {
      background-color: lightgreen;
    }
    .incomplete {
      background-color: lightpink;
    }
    .checkbox-container {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 1;
    }
    .checkbox-container input[type="checkbox"] {
      position: relative;
      transform: scale(1.5);
      cursor: pointer;
    }
	.extra-checkbox-container {
		padding: 1rem;
    }
    .darken {
      filter: brightness(80%); /* Apply 80% brightness to darken the card */
    }
	.counter.green {
      color: green;
    }
    .counter.red {
      color: red;
    }
	.save-button {
      display: inline-block;
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">ZeroTrust Game</a>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
    </ul>
  </nav>
  
  <div class="container mt-5">
    <h1>Welcome, <?php echo $currentTeamName; ?>!</h1>
    <h2>Team Information</h2>
    <p><strong>Team Name:</strong> <?php echo $currentTeamName; ?></p>

    <h2>Change Team Name</h2>
    <form action="teams.php" method="post">
      <div class="form-group">
        <label for="new_team_name">New Team Name:</label>
        <input type="text" class="form-control" id="new_team_name" name="new_team_name" required>
      </div>
      <button type="submit" class="btn btn-primary">Change Name</button>
    </form>

    <h2>Sprint Progress</h2>
    <table class="table">
      <thead>
        <tr>
          <th>Sprint</th>
          <th>Budget</th>
          <th>Time in Sprint</th>
          <th>Security</th>
          <th>Revenue</th>
          <th>Stability</th>
          <th>Resilience</th>
          <th>Overall Score</th>
        </tr>
      </thead>
      <tbody>
        <?php for ($sprint = 1; $sprint <= 8; $sprint++) { ?>
          <tr>
            <td><?php echo $sprint; ?></td>
            <?php foreach ($currentSkills as $skill) {
              $skillData = explode('=', $skill);
              $skillName = $skillData[0];
              $skillValue = $skillData[1];
            ?>
              <td><?php echo $skillValue; ?></td>
            <?php } ?>
            <td><?php echo $overallScore; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
	
    <h2>Backlog</h2>
	<div>
	  Total Selected Hours: <span id="total-hours">0</span>
	  <button class="save-button btn btn-primary" onclick="saveSelectedStories()">Save for Next Sprint</button>
	</div>
    <div class="card-container">
      <?php foreach ($userStories as $index => $story) { ?>
        <?php if ($story['completed'] === 'False') { ?>
          <div class="card incomplete">
            <?php if ($story['completed'] === 'False') { ?>
              <div class="checkbox-container">
                <input type="checkbox" class="card-checkbox" data-card-index="<?php echo $index; ?>">
              </div>
            <?php } ?>
            <div class="card-body">
              <h5 class="card-title"><strong>(<?php echo $story['storyid']; ?>) <?php echo $story['description']; ?></strong></h5>
              <ul>
                <li>Complexity: <?php echo $story['complexity']; ?></li>
                <li>Revenue: <?php echo $story['revenue']; ?></li>
                <li>Time: <?php echo $story['time']; ?> hours</li>
              </ul>
            </div>
			<div class="extra-checkbox-container">
              <label for="security-<?php echo $index; ?>">
                <input type="checkbox" id="security-<?php echo $index; ?>" class="security-checkbox" data-card-index="<?php echo $index; ?>"> Security
              </label>
              <label for="automation-<?php echo $index; ?>">
                <input type="checkbox" id="automation-<?php echo $index; ?>" class="automation-checkbox" data-card-index="<?php echo $index; ?>"> Automation
              </label>
              <label for="testing-<?php echo $index; ?>">
                <input type="checkbox" id="testing-<?php echo $index; ?>" class="testing-checkbox" data-card-index="<?php echo $index; ?>"> Testing
              </label>
              <label for="cloud-native-<?php echo $index; ?>">
                <input type="checkbox" id="cloud-native-<?php echo $index; ?>" class="cloud-native-checkbox" data-card-index="<?php echo $index; ?>"> Cloud Native
              </label>
            </div>
          </div>
        <?php } else { ?>
          <div class="card completed">
            <div class="card-body">
              <h5 class="card-title"><strong>(<?php echo $story['storyid']; ?>) <?php echo $story['description']; ?></strong></h5>
              <ul>
                <li>Complexity: <?php echo $story['complexity']; ?></li>
                <li>Revenue: <?php echo $story['revenue']; ?></li>
                <li>Time: <?php echo $story['time']; ?> hours</li>
              </ul>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
	
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.card-checkbox').change(function() {
        var cardIndex = $(this).data('card-index');
        var card = $('.card').eq(cardIndex);
        var totalHours = 0;

        if ($(this).is(':checked')) {
          card.addClass('darken');
        } else {
          card.removeClass('darken');
        }

        $('.card-checkbox:checked').each(function() {
          var selectedCardIndex = $(this).data('card-index');
          var selectedCard = $('.card').eq(selectedCardIndex);
          totalHours += parseInt(selectedCard.find('li:nth-child(3)').text().split(' ')[1]);
        });

        $('#total-hours').text(totalHours);
      });
    });
  </script>
</body>
</html>
