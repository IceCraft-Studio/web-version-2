<?php
const DB_HOST = getenv("DB_HOSTNAME");
const DB_NAME = getenv("DB_DATABASE");
const DB_USER = getenv("DB_USERNAME");
const DB_PASS = getenv("DB_PASSWORD");
// pokusim se pripojit k DB stroji
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$link) {
    echo "Nepodařilo se spojit s DB.<br>";
    echo mysqli_connect_error();
    exit;
}