<?php
/**
 * 2 days in seconds.
 */
const DEFAULT_SESSION_LENGTH = 60*60*24*2;
const TOKEN_COOKIE_NAME = 'token';

/**
 * Creates a new session entry in the database and returns its token.
 * @param mixed $username Username of the user to create the session for.
 * @param mixed $password Password of the user to create the session for.
 * @param int $duration Duration of the session in seconds. Deafult is 2 days.
 * @return string|null Session token.
 */
function createSession($username,$password,$duration = DEFAULT_SESSION_LENGTH) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    // Generate Extremely Random Token
    $timeNow = time();
    $timeExpire = $timeNow + $duration;
    $timestamp = date('Y-m-d H:i:s', $timeNow);
    $expires = date('Y-m-d H:i:s', $timeExpire);
    $firstBytes = rand(1,15);
    $token = 
        bin2hex(random_bytes($firstBytes)) . 
        hash('sha384', 'token' . $username . $password . (string)$timeNow . bin2hex(random_bytes(8))) . 
        bin2hex(random_bytes(16-$firstBytes));
    
    $stmt = $dbConnection->prepare('INSERT INTO `session` (`token`,`username`,`timestamp`,`expires`) VALUES (?, ?, ?, ?)');
    $stmt->bind_param("ssss", $token, $username, $timestamp, $expires);
    $stmt->execute();
    $stmt->close();
    //!! Check if it was saved to database
    return $token;
}

/**
 * Verifies session's existance and returns the username for the session.
 * @param string $token Token of the session to verify.
 * @return string|null The username or null if the session's invalid.
 */
function verifySession($token = '') {
    if ($token == '') {
        return null;
    }
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $stmt = $dbConnection->prepare("SELECT `username`, `expires` FROM `session` WHERE `token` = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $username = $row['username'];
        $expires = $row['expires'];
    } else {
        $username = null;
    } 
    $stmt->close();
    
    if ($expires !== null && strtotime($expires) < time()) {
        destroySession($token);
        return null;
    }

    return $username;
}

/**
 * Removes a session based on the token.
 * @param string $token Token of the session to remove.
 * @return void 
 */
function destroySession($token) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    dbQuery($dbConnection,'DELETE FROM `session` WHERE `token` = ?',"s",[$token]);
}

/**
 * Removes sessions based on the username.
 * @param string $username Username to remove all sessions of.
 * @return void 
 */
function destroyUserSessions($username) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    dbQuery($dbConnection,'DELETE FROM `session` WHERE `username` = ?',"s",[$username]);
}

/**
 * Runs the `setcookie` function with all the correct parameters to update the session token.
 * @param mixed $token The session token.
 * @param int $duration Amount of seconds the session lasts for.
 * @return void
 */
function updateSessionCookie($token,$duration = DEFAULT_SESSION_LENGTH) {
    setcookie(TOKEN_COOKIE_NAME,$token,expires_or_options:time()+$duration,path:'/~dobiapa2',secure:true);
}