<?php
/**
 * Types of image file allowed as the user's profile picture.
 * @var array
 */
const ALLOWED_PROFILE_PICTURE_IMG_TYPES =  [
    IMAGETYPE_PNG,
    IMAGETYPE_JPEG,
    IMAGETYPE_WEBP  
];
/**
 * The starting portion of the URL for downloading a profile picture.
 * @var string
 */
const PROFILE_PICTURE_URL_START = '/~dobiapa2/api/internal/users/profile-picture.php';
/**
 * The starting portion of the URL for accessing user's profile.
 * @var string
 */
const USER_PAGE_URL_START = '/~dobiapa2/users/';

/**
 * Values for the different user roles in the database.
 */
enum UserRole: string
{
    case Banned = 'ban';
    case User = 'user';
    case Admin = 'admin';
    case Owner = 'owner';
}
/**
 * Values for the social media link columns in the database.
 */
enum UserSocial: string
{
    case Reddit = 'reddit';
    case Twitter = 'twitter';
    case Instagram = 'instagram';
    case Discord = 'discord';
    case Website = 'website';
}
/**
 * Sort values for the SQL query used to get list of projects.
 */
enum UserSort: string{
    case Username = 'username';
    case DisplayName = 'display_name';
    case Role = 'role';
    case Created = 'datetime_created';
}

/**
 * Returns the URL for the profile of the given user.
 * @param string $username The user's username.
 * @return string The result URL.
 */
function getUserLink($username) {
    return USER_PAGE_URL_START . $username;
}

/**
 * Returns the URL for the profile picture of the given user.
 * @param string $username The user's username.
 * @param bool $full If `true`, return the URL for the full resolution picture.
 * @return string The result URL.
 */
function getUserPictureLink($username,$full = false) {
    return PROFILE_PICTURE_URL_START . '?username=' . $username . ($full ? '' : '&variant=preview');
}

/**
 * Retuns the data directory for a given user and ensures it exists. (Optionally with subdirectories.)
 * @param string $username The user's username.
 * @param string $subdir Optional subdirectory inside the user folder.
 * @return string|bool The directory, `false` on failure.
 */
function getUserDirectory($username,$subdir = '') {
    $userDirectory = resolveDataPath('user/' . $username);
    if ($subdir != '') {
        $userDirectory .= '/' . $subdir;
    }
    if (!is_dir($userDirectory)) {
        if (!mkdir($userDirectory,0777,true)) {
            return false;
        }
    }
    return $userDirectory;
}

//## Creating and Deleting User
/**
 * Creates a new user with given username, password and role.
 * @param string $username The new user's username.
 * @param string $password The new user's password.
 * @param UserRole $role The new user's role.
 * @return bool `true` on success, `false` on failure.
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
 * Runs SQL command to delete the user from the database and recursively deletes all their files from the file system.
 * @param string $username The user's username.
 * @return void
 */
function deleteUser($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $resultUser = dbQuery($dbConnection, "DELETE FROM `user` WHERE `username` = ?", "ss", [$username]);
    removeDirRecursive(getUserDirectory($username));
}

//## Validations
/**
 * Returns `true` when the string is between 4 and 48 characters long (inclusive) and is safe to use as a URL (uses `isStringSafeUrl()` function), otherwise `false`. 
 * @param string $username The string to test.
 * @return bool The result of the validation.
 */
function validateUsername($username)
{
    if (4 > strlen($username) || strlen($username) > 48) {
        return false;
    }
    return isStringSafeUrl($username);
}

/**
 * Returns `true` when the string is less than 112 characters (inclusive), otherwise `false`. 
 * @param string $displayName The string to test.
 * @return bool The result of the validation.
 */
function validateUserDisplayName($displayName) {
    return strlen($displayName) <= 64;
}

/**
 * Returns `true` when the string is less than 200 characters (inclusive) and valid as an email when `!= ''`, otherwise `false`. 
 * @param string $username The string to test.
 * @return bool The result of the validation.
 */
function validateUserEmail($email) {
    return (filter_var($email,FILTER_VALIDATE_EMAIL) !== false || $email == '') && strlen($email) <= 200;
}

/**
 * Returns `true` when the string is less than 150 characters (inclusive), otherwise `false`. 
 * @param string $social The string to test.
 * @return bool The result of the validation.
 */

function validateUserSocial($social) {
    return strlen($social) <= 150;
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
 * Takes a specified file on the server and sets it as the profile picture for the given user. If empty deletes the file.
 * @param mixed $username Username to update it for.
 * @param mixed $fileLocation Where the image file is currently stored.
 * @return bool `true` on success, `false` on failure.
 */
function saveUserProfilePicture($username, $fileLocation)
{
    $userDirectory = getUserDirectory($username);
    if ($userDirectory === false) {
        return false;
    }
    $profilePictureFullPath = $userDirectory . '/profile-picture';
    $profilePicturePreviewPath = $userDirectory . '/profile-picture-preview';

    if ($fileLocation == '') {
        if (file_exists($profilePictureFullPath)) {
            unlink(($profilePictureFullPath));
        }
        if (file_exists($profilePicturePreviewPath)) {
            unlink(($profilePicturePreviewPath));
        }
        return true;
    }

    return (
        saveImageAsWebpOrGif($fileLocation,$profilePictureFullPath, 384, 384) &&
        saveImageAsWebpOrGif($fileLocation,$profilePicturePreviewPath, 96, 96)
    );
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
function changeUserSocial($username, $social, $newLink)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $socialName = $social->value;
    $result = dbQuery($dbConnection, "UPDATE `user` SET `social_$socialName` = ? WHERE `username` = ?", "ss", [$newLink, $username]);
    return ($result !== false && $result !== 0);
}

/**
 * Verifies the user's current password and changes it to a new one.
 * @param string $username Username to change the password for.
 * @param string $oldPassword Their old password, must verify successfully.
 * @param string $newPassword Their new password.
 * @return bool `true` if the password was successfully changes, `false` otherwise.
 */
function changeUserPassword($username, $newPassword)
{
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `user` SET `password_hash` = ? WHERE `username` = ? ", "ss", [$newHash, $username]);
    return ($result !== false && $result !== 0);
}

//## Retrieving User Information
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
 * Retrieves all user data from the database.
 * @param string $username Username to look for in the database.
 * @return array|bool All columns of the user from the database or `false` if the user isn't found.
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
 * Returns list of users from the database defined by page number, items per page and filtering & sorting parameters.
 * @param int $listNumber The page number.
 * @param int $listItems Amount of items per page.
 * @param array<string> $filters Array with 1 index, `role`. If the string isn't empty, it is used to filter out results.
 * @param UserSort $sortBy How to sort the users.
 * @param bool $sortAscending When `true` 'ASC' is used in the SQL query.
 * @return array|bool Array of users based on the parameters, `false` on failure.
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
 * Returns the amount of users.
 * @param array $filters Array with 1 index, `role`. If the string isn't empty, it is used to filter out results.
 * @return int|bool `false` on failure, otherwise the amount of user records in the databse.
 */
function getUserCount($filters = ['role' => '']) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
     if (($filters['role'] ?? '') == '') {
        $result = dbQuery($dbConnection,"SELECT COUNT(*) AS `total` FROM `user`");
    } else {
        $result = dbQuery($dbConnection,"SELECT COUNT(*) AS `total` FROM `user` WHERE `role` = ?","s",[$filters['role']]);
    }
    if ($result === false || count($result) === 0) {
        return false;
    }
    return (int)$result[0]['total'];
}

//## Roles
/**
 * Retrieves the list of all roles from the database.
 * @return array|bool Array of all roles or `false` if the query fails.
 */
function getRoles() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    return dbQuery($dbConnection,'SELECT * FROM `role`');
}

/**
 * Retrieves the role name for a given ID.
 * @param string $roleId The role ID.
 * @return string|bool Name of the role or `false` if the role isn't found.
 */
function getRoleName($roleId)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, 'SELECT `name` FROM `role` WHERE `id` = ?','s', [$roleId]);
    if ($result === false || count($result) === 0) {
        return false;
    }
    return $result[0]['name'] ?? false;
}
