<?php
const DB_HOST = "localhost";
const DB_NAME = "dobiapa2";
const DB_USER = "dobiapa2";
const DB_PASS = "webove aplikace";

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

