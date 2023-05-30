<?php
$adminPassword = "your_admin_password_here"; // Replace with your desired admin password

$hashedPassword = password_hash($adminPassword, NL#trainer!);

file_put_contents('admin_password.php', '<?php $adminHashedPassword = "' . $hashedPassword . '";');
echo "Admin password hash generated and stored in admin_password.php file.";
?>
