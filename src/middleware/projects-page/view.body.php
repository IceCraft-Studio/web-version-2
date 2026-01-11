<?php

$viewState = ViewData::getInstance();

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

$pageModifiedString = $pageModified === 'Unknown' ? 'Unknown' : date('d/m/Y H:i', strtotime($pageModified));
$pageCreatedString = $pageCreated === 'Unknown' ? 'Unknown' : date('d/m/Y H:i', strtotime($pageCreated));
$pageModifiedTechnical = date("Y-m-d\TH:i",strtotime($pageModified));
$pageCreatedTechnical = date("Y-m-d\TH:i",strtotime($pageCreated));

$pageCategoryName = getCategoryName($pageCategory) ?? 'Unknown';

$pageCreatorData = getUserData($pageUsername);
$pageCreatorDisplayName = ($pageCreatorData['display_name'] ?? '') == '' ? $pageUsername : $pageCreatorData['display_name'];

/**
 * /middleware/projects-page/ - `echo`'s HTML links to project files for downoad.
 * @param array $filesArray The array with the file data.
 * @return void
 */
function gcreateDownloadFiles($filesArray) {
    foreach ($filesArray as $fileRecord) {
        $link = $fileRecord['link'] ?? '';
        if (($fileRecord['display_name'] ?? '') == '') {
            $displayName = $fileRecord['file_name'] ?? 'Unknown File Name';
        } else {
            $displayName = $fileRecord['display_name'];
        }
        echo '<a href="' . $link . '" target="_blank">Download - ' . htmlspecialchars($displayName) . '</a>';
    }
    if (count($filesArray) === 0) {
        echo '<p> No files uploaded to our server for download. </p>';
    }
}

/**
 * /middleware/projects-page/ - `echo`'s HTML links to provided project URLs.
 * @param array $linksArray The array with the link data.
 * @return void
 */
function createDownloadLinks($linksArray) {
    foreach ($linksArray as $linkRecord) {
        $url = $linkRecord['url'] ?? '';
        if (($linkRecord['display_name'] ?? '') == '') {
            $displayName = $url;
        } else {
            $displayName = $linkRecord['display_name'];
        }
        echo '<a href="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" target="_blank">' . htmlspecialchars($displayName) . '</a>';
    }
    if (count($linksArray) === 0) {
        echo '<p> No external links for the project provided. </p>';
    }
}

?>
<main>
    <?php if ($viewerAdmin || $viewerCreator): ?>
    <div class="project-actions">  
        <a href="/~dobiapa2/upload-project?edit-category=<?= $pageCategory ?>&edit-slug=<?= $pageSlug ?>" class="<?= $viewerCreator ? '' : 'hidden'?>">Edit this Project</a>
        <a href="/~dobiapa2/api/internal/projects/delete-project.php?category=<?= $pageCategory ?>&project=<?= $pageSlug ?>">Delete this Project</a>
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
    <img id="full-thumbnail" src="<?= $pageThumbnail ?>" alt="Project Thumbnail">
    <div id="info-row">
        <div>Created on: <time datetime="<?= $pageCreatedTechnical ?>" class="bold"><?= $pageCreatedString ?></time></div>
        <div>Updated on: <time datetime="<?= $pageModifiedTechnical ?>" class="bold"><?=  $pageModifiedString ?></time></div>
        <div>Category: <a href="/~dobiapa2/projects/<?= $pageCategory ?>" class="bold"><?=  $pageCategoryName ?></a></div>
    </div>
    <h2>Description</h2>
    <p class="page-description">
        <?= $pageDescription ?>
    </p>
    <div id="project-article">
        <?= $pageArticle ?>
    </div>
    <h2>Project Links</h2>
    <div id="download-links">
        <?php
            createDownloadLinks($pageLinks);
        ?>
    </div>
    <h2>Project Files</h2>
    <div id="download-files">
        <?php
            gcreateDownloadFiles($pageFiles);
        ?>
    </div>
</main>