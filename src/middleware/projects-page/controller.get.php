<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';

$viewState = ViewData::getInstance();

// Get basic project data
$explodedRoute = explode('/',$viewState->get('normalized-route'));
$projectCategory = $explodedRoute[1];
$projectSlug = $explodedRoute[2];
$projectData = getProjectData($projectCategory,$projectSlug);
if ($projectData === false) {
    http_response_code(404);
    return;
}

// Check if the viewer of the page is admin
$verifiedRole = $viewState->get('verified-role');
$viewerIsAdmin = $verifiedRole === UserRole::Admin->value || $verifiedRole === UserRole::Owner;
$viewState->set('viewer-admin',$viewerIsAdmin);

$viewState->set('page-category',$projectCategory);
$viewState->set('page-slug',$projectSlug);

$viewState->set('page-title',$projectData['title'] ?? 'Title not found!');
$viewState->set('page-description',$projectData['description'] ?? 'Description not Found!');
$viewState->set('page-username',$projectData['username'] ?? '');
$viewState->set('page-thumbnail',getProjectThumbnailLink($projectCategory,$projectSlug,true) ?? '');
$viewState->set('page-article',(loadProjectArticle($projectCategory,$projectSlug) ?? [])['html'] ?? 'Article not found!');
$viewState->set('page-modified',$projectData['datetime_modified'] ?? 'Unknown');
$viewState->set('page-created',$projectData['datetime_created'] ?? 'Unknown');

$viewState->set('page-links',loadProjectLinks($projectCategory,$projectSlug) ?? []);
$viewState->set('page-files',loadProjectFiles($projectCategory,$projectSlug) ?? []);
