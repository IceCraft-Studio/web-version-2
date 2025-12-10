<?php
    $_SESSION['user'] = null;
    session_unset();
    setcookie('PHPSESSID','',time()-1);
?>