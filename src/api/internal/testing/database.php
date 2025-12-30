<?php
require "../../libs/secure/database-env.php";

const DB_HOST = getenv("DB_HOSTNAME");
const DB_NAME = getenv("DB_DATABASE");
const DB_USER = getenv("DB_USERNAME");
const DB_PASS = getenv("DB_PASSWORD");

function connectToDatabase()
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    if (!$connection) {
        echo "Nepodařilo se spojit s DB.<br>";
        echo mysqli_connect_error();
        return;
    } else {
        echo "Asi se podařilo lol.";
    }

    $success = mysqli_select_db($connection, DB_NAME);
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

