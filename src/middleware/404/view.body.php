<?php
$route = trim(parse_url(substr($_SERVER['REQUEST_URI'],11), PHP_URL_PATH),'/');
?>
<main>
    <h1>404 Not Found!</h1>
    <p>Looks like this URL path <span class="bold">/<?= $route; ?></span> doesn't exist on our website.</p>
    <img src="/~dobiapa2/assets/cracked-ice.webp" alt="Cracked Ice">
</main>