<?php
    $_SESSION['user'] = null;
    session_unset();
    setcookie('PHPSESSID','',date()-1);
?>