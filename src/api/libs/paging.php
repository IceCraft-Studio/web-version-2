<?php
/**
 * Summary of createPageLink
 * @param mixed $requestUrl
 * @param mixed $params
 * @param mixed $page
 * @return string
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
 * Summary of generatePageControls
 * @param mixed $page
 * @param mixed $lastPage
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