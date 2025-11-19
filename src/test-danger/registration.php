<?php
  var_dump($_POST)
  if ()
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
            <input type="text" id="username" name="username" required ->
        </div>

        <div class="row">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required >
        </div>	

        <div class="row">
            <label for="image">Profile Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="row">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="row">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <input type="submit" name="register" value="Register">
</body>
</html>
