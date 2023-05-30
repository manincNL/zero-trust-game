<!DOCTYPE html>
<html>
<head>
  <title>ZeroTrust Game</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    .gold {
      background-color: gold;
    }
    .silver {
      background-color: silver;
    }
    .bronze {
      background-color: #cd7f32;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">ZeroTrust Game</a>
    <ul class="navbar-nav ml-auto">
      <?php
        session_start();
        if (isset($_SESSION['username'])) {
          // User is logged in
          echo '<li class="nav-item"><a class="nav-link" href="teams.php">Team Area</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="?logout=true">Logout</a></li>';
        } else {
          // User is not logged in
          echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="register.php">Create Account/Team</a></li>';
        }

        // Logout functionality
        if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
          session_destroy();
          header('Location: index.php');
          exit();
        }
      ?>
    </ul>
  </nav>
  
  <div class="container mt-5">
    <div class="jumbotron">
      <h1>Welcome to ZeroTrust Game</h1>
      <p>Start playing and test your skills!</p>
      <?php
        if (isset($_SESSION['username'])) {
          // User is logged in
          echo '<a href="teams.php" class="btn btn-primary btn-lg">Join Game</a>';
        } else {
          // User is not logged in
          echo '<a href="login.php" class="btn btn-primary btn-lg">Join Game</a>';
        }
      ?>
    </div>
  </div>

  <div class="container mt-5">
    <h2>Current Teams</h2>
    <table class="table">
      <thead>
        <tr>
          <th>Rank</th>
          <th>Team Name</th>
          <th>Score</th>
        </tr>
      </thead>
      <tbody>
        <?php
          // Read team data from teams.txt file
          $teamsData = file('teams.txt', FILE_IGNORE_NEW_LINES);

          // Create an array to store team information
          $teamInfoArray = array();

          // Extract team name and score from teams data
          foreach ($teamsData as $teamData) {
            $teamInfo = explode(',', $teamData);
            $teamName = $teamInfo[0];
            $teamScore = explode('=', $teamInfo[8])[1]; // Assuming overall score is in position 8
            $teamInfoArray[] = array('name' => $teamName, 'score' => $teamScore);
          }

          // Sort the teams by score in descending order and name in ascending order
          usort($teamInfoArray, function($a, $b) {
            if ($a['score'] == $b['score']) {
              return strcmp($a['name'], $b['name']);
            }
            return $b['score'] - $a['score'];
          });

          // Display the teams with rank and score
          $rank = 1;
          foreach ($teamInfoArray as $teamInfo) {
            $rankClass = '';
            if ($rank == 1) {
              $rankClass = 'gold';
            } elseif ($rank == 2) {
              $rankClass = 'silver';
            } elseif ($rank == 3) {
              $rankClass = 'bronze';
            }

            echo '<tr>';
            echo '<td class="rank ' . $rankClass . '">' . $rank . '</td>';
            echo '<td>' . $teamInfo['name'] . '</td>';
            echo '<td>' . $teamInfo['score'] . '</td>';
            echo '</tr>';

            $rank++;
          }
        ?>
      </tbody>
    </table>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
