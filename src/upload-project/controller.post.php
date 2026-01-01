<?php
// Validation
$csrfLegit = validateCsrf();
if (!$csrfLegit) {
    
}



// Processing

redirect('/~dobiapa2/projects/' . $category . '/' . $slug);