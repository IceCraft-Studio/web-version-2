<?php
session_start();
$_SESSION['csrf-token'] = bin2hex(random_bytes(32));