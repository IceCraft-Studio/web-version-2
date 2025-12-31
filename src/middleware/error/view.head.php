<?php
$viewState = ViewData::getInstance();
$statusMessage = $viewState->get('response-message');
?>
<link href="/~dobiapa2/middleware/error/style.css" rel="stylesheet" type="text/css">
<title><?= $statusMessage ?>! | IceCraft Studio</title>