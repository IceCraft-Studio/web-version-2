<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// Validation
session_start();
if (!isset($_POST['csrf-token']) || !isset($_SESSION['csrf-token']) || ($_POST['csrf-token'] !== $_SESSION['csrf-token'])) {
    $_SESSION['csrf-token'] = bin2hex(random_bytes(32));
    setcookie('csrf-violation','1',time()+60);
    exit;
}
setcookie('csrf-violation','0',time()+60);
session_write_close();

// Processing

