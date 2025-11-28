<?php
$route = trim(parse_url(substr($_SERVER['REQUEST_URI'],11), PHP_URL_PATH),'/');
?>
<main>
    <h1>404 Not Found!</h1>
    <p>Looks like this URL doesn't exist on our website.</p>
    <p>Path /<?= $route; ?> can't be found.</p>
    <img src="/~dobiapa2/assets/cracked-ice.webp" alt="Cracked Ice">
</main>