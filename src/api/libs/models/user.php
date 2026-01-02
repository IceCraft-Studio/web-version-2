<?php

enum UserRole: string
{
    case Banned = 'ban';
    case User = 'user';
    case Admin = 'admin';
    case Owner = 'owner';
}

enum UserSocial: string
{
    case Reddit = 'reddit';
    case Twitter = 'twitter';
    case Instagram = 'instagram';
    case Discord = 'discord';
    case Website = 'website';
}

enum UserSort: string{
    case Username = 'username';
    case DisplayName = 'display_name';
    case Role = 'role';
    case Created = 'datetime_created';
}


/**
 * Summary of createUser
 * @param mixed $username
 * @param mixed $password
 * @param UserRole $role
 * @return bool
 */
function createUser($username, $password, $role = UserRole::User)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $timestamp = date('Y-m-d H:i:s');
    $result = dbQuery($dbConnection, 'INSERT INTO `user` (`username`, `password_hash`, `role`,`datetime_created`) VALUES (?, ?, ?, ?)', "ssss", [$username, $passwordHash, $role->value, $timestamp]);
    if ($result === 1) {
        return true;
    }
    return false;
}

/**
 * Returns `true` when the string is between 4 and 48 characters long (inclusive) and is safe to use as a URL (uses `isStringSafeUrl()` function), otherwise `false`. 
 * @param string $username The string to test.
 * @return bool The result of the test.
 */
function validateUsername($username)
{
    if (4 > strlen($username) || strlen($username) > 48) {
        return false;
    }
    return isStringSafeUrl($username);
}

/**
 * Returns `true` when the string is at least 8 characters long and has at least 1 uppercase letter, lowercase letter and number, otherwise `false`. 
 * @param string $password The string to test.
 * @return bool The result of the test.
 */
function validatePassword($password)
{
    return (
        strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) > 0 &&
        preg_match('/[a-z]/', $password) > 0 &&
        preg_match('/[0-9]/', $password) > 0
    );
}

/**
 * Retrieves all user data from the database.
 * @param string $username Username to look for in the database.
 * @return array|bool All rows of the user from the database or `false` if the user isn't found.
 */
function getUserData($username)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT * FROM `user` WHERE `username` = ? LIMIT 1", "s", [$username]);
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
function saveUserProfilePicture($username, $fileLocation)
{
    if ($fileLocation) {

    }
}

/**
 * Updates user's role to a new value.
 * @param string $username Username to update.
 * @param string $newRole New role to set.
 * @return bool `true` if the change was successful, otherwise `false`.
 */
function changeUserRole($username, $newRole)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `user` SET `role` = ? WHERE `username` = ?", "ss", [$newRole, $username]);
    return ($result !== false && $result !== 0);
}

/**
 * Updates user's display name to a new value.
 * @param string $username Username to update.
 * @param string $newDisplayName New display name to set.
 * @return bool `true` if the change was successful, otherwise `false`.
 */
function changeUserDisplayName($username, $newDisplayName)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `user` SET `display_name` = ? WHERE `username` = ?", "ss", [$newDisplayName, $username]);
    return ($result !== false && $result !== 0);
}

/**
 * Updates user's email to a new value.
 * @param string $username Username to update.
 * @param string $newEmail New email to set.
 * @return bool `true` if the change was successful, otherwise `false`.
 */
function changeUserEmail($username,$newEmail)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `user` SET `email` = ? WHERE `username` = ?", "ss", [$newEmail, $username]);
    return ($result !== false && $result !== 0);
}


/**
 * Updates user's social media link to a new value.
 * @param string $username Username to update.
 * @param UserSocial $social Social media to update.
 * @param string $newLink New link to set.
 * @return bool `true` if the change was successful, otherwise `false`.
 */
function changeUserSocials($username, $social, $newLink)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $socialName = $social->value;
    $result = dbQuery($dbConnection, "UPDATE `user` SET `social_$socialName` = ? WHERE `username` = ?", "ss", [$newLink, $username]);
    return ($result !== false && $result !== 0);
}

/**
 * Checks if the provided password corresponds to the provided username.
 * @param string $username Username to verify.
 * @param string $password Password to verify.
 * @return bool `true` if the user exists and their password is correct, otherwise `false`.
 */
function verifyUserPassword($username, $password)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT `password_hash` FROM `user` WHERE `username` = ? LIMIT 1", "s", [$username]);
    if (!$result || count($result) === 0) {
        return false;
    }
    $passwordHash = $result[0]['password_hash'];
    return password_verify($password, $passwordHash);
}

/**
 * Verifies the user's current password and changes it to a new one.
 * @param string $username Username to change the password for.
 * @param string $oldPassword Their old password, must verify successfully.
 * @param string $newPassword Their new password.
 * @return bool `true` if the password was successfully changes, `false` otherwise.
 */
function changeUserPassword($username, $oldPassword, $newPassword)
{
    if (!verifyUserPassword($username, $oldPassword)) {
        return false;
    }
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `user` SET `password_hash` = ? WHERE `username` = ? ", "ss", [$newHash, $username]);
    return ($result !== false && $result !== 0);
}

/**
 * Returns list of users from the database defined by page number, items per page and filtering & sorting parameters.
 * @param int $listNumber The page number.
 * @param int $listItems Amount of items per page.
 * @param array<string> $filters Array with 2 indexes, `username` and `category`. If the string isn't empty, it is used to filter out results.
 * @param UserSort $sortBy How to sort the users.
 * @param bool $sortAscending When `true` 'ASC' is used in the SQL query.
 * @return array
 */
function getUserList($listNumber, $listItems, $filters = ['role' => ''], $sortBy = UserSort::Modified, $sortAscending = false) { 
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $order = $sortAscending ? 'ASC' : 'DESC';
    $offset = ($listNumber - 1) * $listItems;

    $firstSortColumn = '`' . $sortBy->value . '` ' . $order;
    $secondSortColumn = $firstSortColumn == 'role' ? ', `username` ' . $order : '';
    $sortColumns = $firstSortColumn . $secondSortColumn;

    if (($filters['role'] ?? '') == '') {
        return dbQuery($dbConnection,"SELECT * FROM `user` ORDER BY $sortColumns LIMIT ? OFFSET ? ","ii",[$listItems,$offset]);
    }
    return dbQuery($dbConnection,"SELECT * FROM `user` WHERE `role` = ? ORDER BY $sortColumns LIMIT ? OFFSET ? ","sii",[$filters['role'],$listItems,$offset]);
}

/**
 * Retrieves the list of all roles from the database.
 * @return array|bool Array of all roles or `false` if the query fails.
 */
function getRoles() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    return dbQuery($dbConnection,'SELECT * FROM `role`');
}
