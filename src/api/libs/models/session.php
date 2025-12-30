<?php
const DB_ACCESS = getDbAccessObject();


function createSession() {
    $dbConnection = DbConnect::getConnection(DB_ACCESS);

}

function getSession() {
    $dbConnection = DbConnect::getConnection(DB_ACCESS);
    if ($_COOKIE['token']) {
        return true;
    }
}

function verifySession() {
    $dbConnection = DbConnect::getConnection(DB_ACCESS);

}

function destroySession() {
    $dbConnection = DbConnect::getConnection(DB_ACCESS);
}