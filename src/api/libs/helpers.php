<?php
const DATA_PATH = "/home/dobiapa2/data";

/**
 * Removes the URL Path prefix (~dobiapa2), trims the last `/` and makes it lowercase.
 * This makes it useful to work with it in the context of a file system.
 * @param string $uriPath URI Path, usually from `$_SERVER['REQUEST_URI']`.
 * @return string Normalizes URI Route.
 */
function normalizeUriRoute($uriPath)
{
    return strtolower(
        trim(
            parse_url(
                substr($uriPath, 11),
                PHP_URL_PATH
            ),
            '/'
        )
    );
}

/**
 * Returns a full path composed of constant root data directory and a subpath inside of it.
 * @param string $subpath The subpath inside the root directory.
 * @return string The full path.
 */
function resolveDataPath($subpath)
{
    if (str_starts_with($subpath, '/'))
        return DATA_PATH . $subpath;
    return DATA_PATH . '/' . $subpath;
}

/**
 * Returns an object with keys needed for access to database and values taken from environment variables that should hold those secret details.
 * @return object
 */
function getDbAccessObject()
{
    return (object) [
        'hostname' => getenv("DB_HOSTNAME"),
        'database' => getenv("DB_DATABASE"),
        'username' => getenv("DB_USERNAME"),
        'password' => getenv("DB_PASSWORD")
    ];
}

/**
 * Initializes the CSRF token into the `$_SESSION['csrf-token']`.
 * @param bool $force Set to `true` to force replacing an exisiting token. 
 * @return void
 */
function initCsrf($force = false)
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    if (isset($_SESSION['csrf-token']) && !$force) {
        return;
    }
    $_SESSION['csrf-token'] = bin2hex(random_bytes(32));
}

/**
 * Validates the CSRF by comparing the value in `$_POST` and `$_SESSION`.
 * @return bool `true` when valid, `false` when invalid.
 */
function validateCsrf()
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    if (!isset($_POST['csrf-token']) || !isset($_SESSION['csrf-token']) || ($_POST['csrf-token'] !== $_SESSION['csrf-token'])) {
        initCsrf(true);
        return false;
    }
    return true;
}

/**
 * Makes a safe SQL query with parameters and error handling. 
 * @param mysqli $connection Estabilished SQL DB connection.
 * @param string $sqlQuery The SQL query for the database.
 * @param string $types `$types` for `bind_param` method.
 * @param array<mixed> $parameters `$var`(`$vars`) for `bind_param` method.
 * @return array|int|bool The full result of the query - all rows. Number if affected rows for queries that don't return rows. `false` on failure. 
 */
function dbQuery($connection, $sqlQuery, $types = "", $parameters = [])
{
    // Compose and run the query
    $stmt = $connection->prepare($sqlQuery);
    if (!$stmt)
        return false;
    if ($types !== "") {
        if (strlen($types) != count($parameters)) {
            throw new RuntimeException("Types must match the parameters.");
        }
        $bindStatus = $stmt->bind_param($types, ...$parameters);
        if (!$bindStatus)
            return false;
    }
    $executeStatus = $stmt->execute();
    if (!$executeStatus)
        return false;
    // Get results
    $result = $stmt->get_result();
    $returnValue = $result === false ? $stmt->affected_rows : $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $returnValue;
}

/**
 * Checks if the given string is to be used as a safe and fine looking URL.
 * @param string $str
 * @return bool
 */
function isStringSafeUrl($str) {
    return (preg_match_all('/^[a-z0-9]+(-[a-z0-9]+)*$/',$str) > 0);
}

/**
 * Returns the same string with any potential new line removed.
 * @param string $str
 * @return string
 */
function removeNewLines($str) {
    return preg_replace('/\R/', '', $str);
}