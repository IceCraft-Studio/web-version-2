<?php
session_start();
if (!isset($_POST['csrf-token']) || !isset($_SESSION['csrf-token']) || ($_POST['csrf-token'] !== $_SESSION['csrf-token'])) {
    $_SESSION['csrf-token'] = bin2hex(random_bytes(32));
    return;
}