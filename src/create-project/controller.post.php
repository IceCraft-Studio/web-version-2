<?php
// Validation
session_start();
if (!isset($_POST['csrf-token']) || !isset($_SESSION['csrf-token']) || ($_POST['csrf-token'] !== $_SESSION['csrf-token'])) {
    $_SESSION['csrf-token'] = bin2hex(random_bytes(32));
    setcookie('csrf-violation', '1', time() + 60);
    return;
}

setcookie('csrf-violation', '0', time() + 60);
header('Location: /projects', true, 302);

// Processing

