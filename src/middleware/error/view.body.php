<?php
$route = trim(parse_url(substr($_SERVER['REQUEST_URI'],11), PHP_URL_PATH),'/');
$statusCode = http_response_code();
$viewState = ViewData::getInstance();
$errorImage = $viewState->get('error-image');
$statusMessage = $viewState->get('response-message');
?>
<main>
    <h1><?= $statusCode ?> <?= $statusMessage ?>!</h1>
    <?php if ($statusCode === 404): ?>
        <p>Looks like this URL path <span class="bold">/<?= $route; ?></span> doesn't exist on our website.</p>
    <?php endif; ?>
    <?php if ($statusCode === 405): ?>
        <p>You can't use method <span class="bold"><?= $_SERVER['REQUEST_METHOD']; ?></span> on this URL <span class="bold">/<?= $route; ?></span>.</p>
    <?php endif; ?>
    <img src="<?= $errorImage ?>" alt="Error Illustration">
</main>