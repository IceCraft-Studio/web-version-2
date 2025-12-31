<?php
// Validation
$csrfLegit = validateCsrf();
setcookie('legit',(string)$csrfLegit,expires_or_options: time() + 120);

header('Location: /projects', true, 302);

// Processing

