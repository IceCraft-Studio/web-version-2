<?php
$csrfLegit = validateCsrf();

// Load user data at the end.
include __DIR__ . '/controller.get.php';