<?php
$viewState = ViewData::getInstance();
$projectsCategory = $viewState->get('projects-category','');
$projectsCategoryName = $viewState->get('projects-category-name','');

$projectsPageTitle = 'All Projects';
if ($projectsCategory != '') {
    $projectsPageTitle = 'Projects - ' . $projectsCategoryName;
}
?>
<title><?= $projectsPageTitle ?> | IceCraft Studio</title>
<link href="/~dobiapa2/middleware/projects/style.css" rel="stylesheet" type="text/css">