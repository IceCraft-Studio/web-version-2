<?php
    require_once('_db_file.php');
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List</title>
    <style>
        table { border: 1px solid black; border-collapse: collapse; }
        td, th { border: 1px solid black; padding: 5px; }
    </style>
</head>
<body>
    <!-- TODO: vypsat logout odkaz  a jmeno uzivatele -->
     <header>
    <?php
        if (isset($_SESSION['user'])) {
            echo '<p>' . htmlspecialchars($_SESSION['user']['name']) . '</p><a href="logout.php">Logout</a>';
        } else {
            echo '<a href="login.php">Login</a>';
        }

    ?>
    </header>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Content</th>
        </tr>
        <?php
            // TODO: jak ziskam notes uzivatele?
            
        ?>
    </table>
    <?php
        // TODO: pridat odkaz na add.php pokud je uzivatel prihlasen
            echo '<a href="add.php">Add note</a>';
       
    ?>
</body>
</html>