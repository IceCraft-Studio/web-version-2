<?php
    session_start();
    if (isset($_POST['username'])) {
        $_SESSION['username'] = $_POST['username'];
    }
    if (isset($_POST['theme'])) {
        $_SESSION['theme'] = $_POST['theme'];
    }
    // Cookies or headers can only be set before any body contents (i.e. HTML) is sent!!!!!!
    if (isset($_COOKIE['counter'])) {
        $count = $_COOKIE['counter'] + 1;
    } else {
        $count = 1;
    }
    setcookie('counter',"$count",time()+24*60*60,"/~dobiapa2/test-danger");
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_SESSION['theme']) ? htmlspecialchars($_SESSION['theme']) : 'dark'?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session</title>
    <style>
        :root[data-theme="light"] body {
            background-color: wheat;
            color: black;
        }
        :root[data-theme="dark"] body {
            background-color: darkblue;
            color: white;
        }
    </style>
</head>
<body>
    <form action="/~dobiapa2/test-danger/session.php" method="POST">
        <input type="text" name="username">
        <select name="theme">
            <option value="light" <?=isset($_SESSION['theme']) && $_SESSION['theme'] == 'light' ? 'selected' : '';?>light</option>
            <option value="dark" <?=isset($_SESSION['theme']) && $_SESSION['theme'] == 'dark' ? 'selected' : '';?>>dark</option>
        </select>
        <input type="submit" name="sent" value="true">
    </form>
<div>Tuto stránku jste již navštívili <?= isset($_COOKIE['counter']) ? htmlspecialchars($_COOKIE['counter']) : "0"; ?> krát.</div>
<div>Session [username]=<?=isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';?></div>
</body>
</html>