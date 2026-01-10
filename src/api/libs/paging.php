<?php
/**
 * Crates links for paging preserving GET URL parameters.
 * @param string $requestUrl The URL of the paging web page.
 * @param array $params The parameters to preserve. Usually `$_GET`.
 * @param string $page The page number for the link.
 * @return string The result URL link.
 */
function createPageLink($requestUrl, $params, $page) {
    $newParams = $params;
    $newParams['page'] = $page;
    $questionMarkIndex = strpos($requestUrl, '?');
    if ($questionMarkIndex !== false) {
        $requestUrl = substr($requestUrl,0,$questionMarkIndex);
    }
    return $requestUrl . '?' . http_build_query($newParams);
}

/**
 * Simple algorithm to `echo` page control links to move to next, previous, first and last page.
 * @param int $page The current page to generate the links for.
 * @param int $lastPage The last page of the listing.
 * @return void
 */
function generatePageControls($page,$lastPage) {
    $firstPage = 1;
    echo "<div>";
    if ($page > $firstPage) {
        $link = createPageLink($_SERVER['REQUEST_URI'],$_GET,$firstPage);
        echo '<a href="' . $link . '">&lt;&lt; First Page</a>';
    }
    if ($page > $firstPage+1) {
        $link = createPageLink($_SERVER['REQUEST_URI'],$_GET,$page-1);
        echo '<a href="' . $link . '">&lt; Previous Page</a>';
    }
    echo "</div><div>";
    if ($page < $lastPage-1) {
        $link = createPageLink($_SERVER['REQUEST_URI'],$_GET,$page+1);
        echo '<a href="' . $link . '">Next Page &gt;</a>';
    }
    if ($page < $lastPage) {
        $link = createPageLink($_SERVER['REQUEST_URI'],$_GET,$lastPage);
        echo '<a href="' . $link . '">Last Page &gt;&gt;</a>';
    }
    echo "</div>";
}