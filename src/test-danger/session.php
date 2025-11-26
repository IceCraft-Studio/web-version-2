<?php
    // Cookies or headers can only be set before any body contents (i.e. HTML) is sent!!!!!!
    if (isset($_COOKIE['counter'])) {
        $count = $_COOKIE['counter'] + 1;
    } else {
        $count = 1;
    }
    setcookie('counter',"$count",time()+24*60*60);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session</title>
</head>
<body>
    <div>Tuto stránku jste již navštívili <?= isset($_COOKIE['counter']) ? htmlspecialchars($_COOKIE['counter']) : "0"; ?> krát.</div>
</body>
</html>