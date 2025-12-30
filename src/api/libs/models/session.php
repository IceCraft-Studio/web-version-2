<?php
/**
 * 2 days in seconds.
 */
const DEFAULT_SESSION_LENGTH = 60*60*24*2;

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

    return $token;
}

/**
 * Verifies session's existance and returns the username for the session.
 * @param string $token Token of the session to verify.
 * @return string|null The username or null if the session's invalid.
 */
function verifySession($token) {
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
 * Removes the session based on the token.
 * @param string $token Token of the session to remove.
 * @return void 
 */
function destroySession($token) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $stmt = $dbConnection->prepare('DELETE FROM `session` WHERE `token` = ?');
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();
}