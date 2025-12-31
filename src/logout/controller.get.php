<?php

if (isset($_COOKIE['token'])) {
    setcookie('token','',expires_or_options:time()-99999,path: '/~dobiapa2',secure: true);
    destroySession($_COOKIE['token']);
}

header('Location: /~dobiapa2/login',true,302);
exit;