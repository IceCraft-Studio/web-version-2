<?php
// Validation
$csrfLegit = validateCsrf();
if (!$csrfLegit) {
    
}



// Processing

header('Location: /~dobiapa2/projects/' . $category . '/' . $slug, true, 302);
exit;