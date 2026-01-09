<?php
$viewState = ViewData::getInstance();
$projectsCategory = $viewState->get('projects-category','');
$projectsCategoryName = $viewState->get('projects-category-name','');

$projectsPageTitle = 'All Projects';
if ($projectCategory !== '') {
    $projectsPageTitle = 'Projects - ' . $projectsCategoryName;
}
?>
<title><?= $projectsPageTitle ?> | IceCraft Studio</title>
<link href="./style.css" rel="stylesheet" type="text/css">