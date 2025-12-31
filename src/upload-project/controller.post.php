<?php
// Validation
$csrfLegit = validateCsrf();
if ($csrfLegit) {

setcookie('legit',"frfr",expires_or_options: time() + 120);
} else {

setcookie('legit',"not at all",expires_or_options: time() + 120);
}

header('Location: /projects', true, 302);

// Processing

