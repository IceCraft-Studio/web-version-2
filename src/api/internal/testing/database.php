<?php
const DB_HOST = "localhost";
const DB_NAME = "dobiapa2";
const DB_USER = "dobiapa2";
const DB_PASS = "webove aplikace";
// pokusim se pripojit k DB stroji
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$link) {
    echo "Nepodařilo se spojit s DB.<br>";
    echo mysqli_connect_error();
    exit;
} else { 
    echo "Asi se podařilo lol."
}