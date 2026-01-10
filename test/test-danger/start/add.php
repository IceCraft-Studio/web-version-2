<?php
    require_once('_db_file.php');
    session_start();
    // TODO: generate csrf token
    

    // TODO: check if user is logged in
    

    if(isset($_POST['submit'])) {
        // check csrf token if it is valid
        
        //TODO add note to db

        //TODO redirect to list.php
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add note</title>
</head>
<body>
    <?php
        //TODO: show user name and logout link if user is logged in
    ?>

    <form action="" method="post">
        <!-- TODO: add csrf token to form -->
        
        <!--TODO: add user_id to form -->
        <input type="hidden" name="user_id" value="">
        <div>
            <label>Title: <input type="text" name="title"></label>
        </div>
        <div>
            <label>Text: <textarea name="text"></textarea></label>
        </div>
        <input type="submit" name="submit" value="Add">
</body>
</html>