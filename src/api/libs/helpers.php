<?php

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

$viewData = ["test" => "something"];