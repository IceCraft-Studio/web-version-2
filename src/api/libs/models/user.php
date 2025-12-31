<?php

enum UserRole: string {
    case Banned = 'ban';
    case User = 'user';
    case Admin = 'admin';
    case Owner = 'owner';
}

enum UserSocial: string {
    case Reddit = 'reddit';
    case Twitter = 'twitter';
    case Instagram = 'instagram';
    case Discord = 'discord';
    case Website = 'website';
}


/**
 * Summary of createUser
 * @param mixed $username
 * @param mixed $password
 * @param UserRole $role
 * @return void
 */
function createUser($username,$password,$role = UserRole::User) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

/**
 * Retrieves all user data from the database.
 * @param string $username Username to look for in the database.
 * @return array|bool All rows of the user from the database or `false` if the user isn't found.
 */
function getUserData($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection,"SELECT * FROM `user` WHERE `username` = ? LIMIT 1","s",[$username]);
    if (!$result || count($result) === 0) {
        return false;
    }
    return $result[0];
}

/**
 * Takes a specified file on the server and sets it as the profile picture for the given user. If empty deleted the file.
 * @param mixed $username
 * @param mixed $fileLocation
 * @return void
 */
function saveUserProfilePicture($username,$fileLocation) {

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


function changeUserSocials($username,$social,$newLink) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

/**
 * Checks if the provided password corresponds to the provided username.
 * @param string $username Username to verify.
 * @param string $password Password to verify.
 * @return bool `true` if the user exists and their password is correct, otherwise `false`.
 */
function verifyUserPassword($username,$password) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection,"SELECT `password_hash` FROM `user` WHERE `username` = ? LIMIT 1","s",[$username]);
    if (!$result || count($result) === 0) {
        return false;
    }
    $passwordHash = $result[0]['password_hash'];
    return password_verify($password,$passwordHash);
}

/**
 * Verifies the user's current password and changes it to a new one.
 * @param string $username Username to change the password for.
 * @param string $oldPassword Their old password, must verify successfully.
 * @param string $newPassword Their new password.
 * @return bool `true` if the password was successfully changes, `false` otherwise.
 */
function changeUserPassword($username,$oldPassword,$newPassword) {
    if (!verifyUserPassword($username,$oldPassword)) {
        return false;
    }
    $newHash = password_hash($newPassword,PASSWORD_BCRYPT);
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection,"UPDATE `user` SET `password_hash` = ? WHERE `username` = ?","ss",[$newHash,$username]);
    return ($result !== false && $result !== 0);
}
