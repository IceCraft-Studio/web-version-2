<?php
const DB_HOST = "localhost";
const DB_NAME = "dobiapa2";
const DB_USER = "dobiapa2";
const DB_PASS = "webove aplikace";

function connectToDatabase()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    if (!$link) {
        echo "Nepodařilo se spojit s DB.<br>";
        echo mysqli_connect_error();
        return;
    } else {
        echo "Asi se podařilo lol.";
    }

    $success = mysqli_select_db($link, DB_NAME);
    if (!$success) {
        echo "Nepodařilo se přepnout na správnou databázi";
        return;
    } else {
        echo "Asi máme databázi";
    }
    return $link;
}

$db = connectToDatabase();
$sqlQuery = "SHOW TABLES;";
$result = mysqli_query($db,$sqlQuery);