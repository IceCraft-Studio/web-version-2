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
function resolveDataPath($subpath) {
    if (str_starts_with($subpath,'/')) return DATA_PATH . $subpath;
    return DATA_PATH . '/' . $subpath;
}

/**
 * Returns an object with keys needed for access to database and values taken from environment variables that should hold those secret details.
 * @return object
 */
function getDbAccessObject() {
    return (object)[
    'hostname' => getenv("DB_HOSTNAME"),
    'database' => getenv("DB_DATABASE"),
    'username' => getenv("DB_USERNAME"),
    'password' => getenv("DB_PASSWORD")
];
}