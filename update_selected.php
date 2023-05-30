<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $selectedIndices = $_POST['indices'];

  // Read the stories data from the file
  $storiesData = file($storiesFile, FILE_IGNORE_NEW_LINES);

  // Update the selected status for the selected indices
  foreach ($selectedIndices as $index) {
    $storyData = explode(',', $storiesData[$index]);
    $storyData[10] = 'Yes'; // Assuming the selected status is in position 10

    // Update the story data in the array
    $storiesData[$index] = implode(',', $storyData);
  }

  // Write the updated stories data back to the file
  file_put_contents($storiesFile, implode("\n", $storiesData));

  // Return a success message
  echo 'success';
}
?>