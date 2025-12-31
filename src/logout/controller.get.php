<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';

if (isset($_COOKIE['token'])) {
    setcookie('token','',expires_or_options:time()-9999,secure: true);
    destroySession($_COOKIE['token']);
}

header('Location: /~dobiapa2/login',true,302);
exit;