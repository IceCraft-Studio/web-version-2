<?php
require "../../libs/secure/database-env.php";

function connectToDatabase()
{
    $connection = mysqli_connect(getenv("DB_HOSTNAME"), getenv("DB_USERNAME"), password: getenv("DB_PASSWORD"));
    if (!$connection) {
        echo "Nepodařilo se spojit s DB.<br>";
        echo mysqli_connect_error();
        return;
    } else {
        echo "Asi se podařilo lol.";
    }

    $success = mysqli_select_db($connection, getenv("DB_DATABASE"));
    if (!$success) {
        echo "Nepodařilo se přepnout na správnou databázi";
        return;
    } else {
        echo "Asi máme databázi";
    }
    return $connection;
}

$db = connectToDatabase();
$sqlQuery = "SHOW TABLES;";
$result = mysqli_query($db,$sqlQuery);

if ($result) {
    while ($row = mysqli_fetch_row($result)) {
        echo $row[0] . "<br>";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

