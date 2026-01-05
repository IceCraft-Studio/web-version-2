<?php
$viewState = ViewData::getInstance();
?>
<main>
    <?= var_dump($viewState->get('users-list')) ?>
</main>