<?php

if (isset($_COOKIE['token'])) {
    updateSessionCookie('',-99999);
    destroySession($_COOKIE['token']);
}

redirect('/~dobiapa2/login');