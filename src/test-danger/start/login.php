<?php
    require_once '_db_file.php';

    // hesla: koko:autobus24, lolo:lolo
    //echo password_hash('lolo', PASSWORD_DEFAULT);
    if(isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];


        $user = get_user($username);
        if ($user) {
            if (password_verify($password,$user['password'])) {
                session_start();
                $_SESSION['user'] = $user;
                session_write_close();
                header('Location: list.php');
            } else {
                $error = "U stupid, wrong username or password!";
            }
        } else {
            $error = "U stupid, wrong username or password!";
        }
        
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="" method="post">
        <?php
            // TODO: add returnurl to form
        ?>
        <div>
        <label>Username: <input autocomplete="off" type="text" value="<?= htmlspecialchars($username ?? ''); ?>" name="username"></label>
        </div>
        <div>
            <label>Password: <input type="password" name="password"></label>
        </div>
        <div class="error">
            <?php if(isset($error))
                echo '<p>'.$error.'</p>';
            ?>
        </div>
        <input type="submit" name="submit" value="Login">

</body>
</html>