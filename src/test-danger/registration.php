<?php
  $trimmed = [];
  foreach ($_POST AS $key=>$value) {
    $trimmed[$key] = trim($value);
  }
  $errorNames = ['Username' => 'Username needs to be more than 4 chars long!'];
  $errors = [];
  if (isset($_POST['register'])) {
    if (strlen($trimmed['username']) < 4) {
        $errors[] = 'username';
    }
    if (!filter_var($trimmed['email'],FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'email';
    }
    if (strlen($trimmed['password']) < 8) {
        $errors[] = 'password';
    }
    if ($trimmed['confirm_password'] != $trimmed['password']) {
        $errors[] = 'confirm_password';
    }
    if (count($errors) == 0) {

    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        .error { color: red; }
        .row { margin-bottom: 10px; }
        label { display: inline-block; width: 150px; }
        .errors { border: 1px solid red; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Registration Form</h1>
    
    <form action="" method="POST" enctype="multipart/form-data">
	<div class="row">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?= isset($trimmed['username']) ? htmlspecialchars($trimmed['username']) : '' ?>">
            <?php
            if (in_array('username', $errors)) {
                echo "<span class=\"error\"> Username needs to be more than 4 chars long!</span>";
            }
            ?>
        </div>

        <div class="row">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required >
            <?php
            if (in_array('email', $errors)) {
                echo "<span class=\"error\"> Format of email is name@domain.tld!</span>";
            }
            ?>
        </div>	

        <div class="row">
            <label for="image">Profile Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="row">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <?php
            if (in_array('password', $errors)) {
                echo "<span class=\"error\"> Password needs to be at least 8 characters long!</span>";
            }
            ?>
        </div>
        <div class="row">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <?php
            if (in_array('confirm_password', $errors)) {
                echo "<span class=\"error\"> Passwords don't match!</span>";
            }
            ?>
        </div>

        <input type="submit" name="register" value="Register">
</body>
</html>
