<?php

// Project Basic Data
$pageCategory = $viewState->get('page-category','');
$pageSlug = $viewState->get('page-slug','');
// More Project Data
$pageTitle = htmlspecialchars($viewState->get('page-title', 'Title not found!'));
$pageDescription = htmlspecialchars($viewState->get('page-description', 'Description not Found!'));
$pageUsername = $viewState->get('page-username', '');
$pageThumbnail = $viewState->get('page-thumbnail', '');
$pageArticle = $viewState->get('page-article', 'Article not found!');
$pageModified = $viewState->get('page-modified','Unknown');
$pageCreated = $viewState->get('page-created','Unknown');
// Project Links and Downloads
$pageLinks = $viewState->get('page-links',[]);
$pageFiles = $viewState->get('page-files',[]);

// Admin and Creator Data
$viewerAdmin = $viewState->get('viewer-admin',false);
$viewerCreator = $viewState->get('verified-username','') === $pageUsername;

$pageModifiedString = $pageModified === 'Unknown' ? 'Unknown' : date('d/m/Y', strtotime($pageModified));
$pageCreatedString = $pageCreated === 'Unknown' ? 'Unknown' : date('d/m/Y', strtotime($pageCreated));
$pageCategoryName = getCategoryName($category) ?? 'Unknown';

$pageCreatorData = getUserData($pageUsername);
$pageCreatorDisplayName = ($pageCreatorData['display_name'] ?? '') == '' ? $pageUsername : $pageCreatorData['display_name'];

function generateDownloadFiles($filesArray) {
    foreach ($filesArray as $fileRecord) {
        $link = $fileRecord['link'] ?? '';
        if (($linkRecord['display_name'] ?? '') == '') {
            $displayName = $fileRecord['file_name'] ?? 'Unknown File Name';
        } else {
            $displayName = $fileRecord['display_name'];
        }
        echo '<a href="' . htmlspecialchars($link) . '" target="_blank">Download - ' . htmlspecialchars($displayName) . '</a>';
    }
}

function generateDownloadLinks($linksArray) {
    foreach ($linksArray as $linkRecord) {
        $url = $linkRecord['url'] ?? '';
        if (($linkRecord['display_name'] ?? '') == '') {
            $displayName = $url;
        } else {
            $displayName = $linkRecord['display_name'];
        }
        echo '<a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($displayName) . '</a>';
    }
}

?>
<main>
    <?php if ($viewerAdmin || $viewerCreator): ?>
    <div class="project-actions">  
        <a href="/~dobiapa2/upload-project?edit-category=<?= $pageCategory ?>&edit-slug=<?= $pageSlug ?>" <?= $viewerCreator ? '' : 'disabled'?>>Edit this Project</a>
        <a href="/~dobiapa2/api/internal/projects/delete-project.php?category=<?= $pageCategory ?>&slug=<?= $pageSlug ?>">Delete this Project</a>
    </div>
    <?php endif ; ?>
    <div class="creator-container">
        <a href="<?= getUserLink($pageUsername) ?>">
            <img src="<?= getUserPictureLink($pageUsername) ?>" alt="<?= $pageCreatorDisplayName ?>'s Profile Picture">
            <span>
                <?= $pageCreatorDisplayName ?>
            </span>
        </a>
    </div>
    <h1 class="page-title">
        <?= $pageTitle ?>
    </h1>
    <img src="<?= $pageThumbnail ?>" alt="Project Thumbnail">
    <div id="info-row">
        <div>Created on: <span class="bold"><?= $pageCreatedString ?></span></div>
        <div>Modified on: <span class="bold"><?=  $pageModifiedString ?></span></div>
        <div>Category: <span class="bold"><?=  $pageCategoryName ?></span></div>
    </div>
    <h2>Description</h2>
    <p class="page-description">
        <?= $pageDescription ?>
    </p>
    <div id="project-article">
        <?= $projectArticle ?>
    </div>
    <h2>Project Links</h2>
    <div id="download-links">
        <?php
            generateDownloadLinks($pageLinks);
        ?>
    </div>
    <h2>Project Files</h2>
    <div id="download-files">
        <?php
            generateDownloadFiles($pageFiles);
        ?>
    </div>
</main>