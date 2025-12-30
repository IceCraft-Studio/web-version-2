<?php
const DB_ACCESS = getDbAccessObject();

function createUser($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

function getUserData($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

function saveUserProfilePicture($username) {

}

function changeUserRole($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

function changeUserDisplayName($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}   

function changeUserEmail($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

function changeUserSocials($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

function changeUserPassword($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

//? Maybe
function validateSocialProfile($social,$profile) {
    switch ($social) {
    case "reddit":
        break;
    case "twitter":
        break;
    case "instagram":
        break;
    default:
        break;
}
}

