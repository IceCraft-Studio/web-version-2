<?php
if (verifySession($_COOKIE['token'] ?? '') != null) {
    header('Location: /~dobiapa2/profile',true,302);
    exit;
}
initCsrf();