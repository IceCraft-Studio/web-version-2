<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/paging.php';

$viewState = ViewData::getInstance();

$viewerUsername = $viewState->get('verified-username','');

// Project Data
$projectsList = $viewState->get('projects-list',[]);
$projectsCategory = $viewState->get('projects-category','');
$projectsCategoryName = $viewState->get('projects-category-name','');

$projectsPageTitle = 'All Projects';
if ($projectsCategory !== '') {
    $projectsPageTitle = 'Projects - ' . $projectsCategoryName;
}

// Paging Data
$lastPageNumber = $viewState->get('paging-last-page',1);
$currentPageNumber = $viewState->get('paging-page',1);

$defaultSizes = ['6', '20', '30', '60', '100'];

$currentSize = (string) ($viewState->get('paging-size', 20));
if (!in_array($currentSize, $defaultSizes)) {
    $defaultSizes[] = $currentSize;
}
$currentSort = $viewState->get('paging-sort','title');
$currentOrder = $viewState->get('paging-order','asc');


function generateProjectCard($category,$slug,$title,$description,$thumbnailLink,$username,$modified) {
    $projectLink = getProjectLink($category,$slug);

    $userData = getUserData($username);
    if (($userData['display_name'] ?? '')) {
        $displayName = $username;
    } else {
        $displayName = htmlspecialchars($userData['display_name']);
    }
    $userLink = getUserLink($username);
    $userPictureLink = getUserPictureLink($username);

    $datetimeTechnical = date("Y-m-d",strtotime($modified));
    $datetimeHuman = date("d/m/Y",strtotime($modified));

    return '
<div class="project-card">
    <div class="user-part">
        <a href="' . $userLink . '">
            <img src="' .  $userPictureLink . '">
            <span>' . $displayName . '</span>
        </a>
    </div>
    <a href="' . $projectLink . '">
        <div class="project-part">
            <img src="' . htmlspecialchars($thumbnailLink) . '" alt="Project Card Thumbnail">
            <h3 title="' . htmlspecialchars($title) . '">' . htmlspecialchars($title) . '</h3>
            <p class="description" title="' . htmlspecialchars($description) . '">' . htmlspecialchars($description) . '</p>
            <p class="modified">Date Modified: <time datetime="' . $datetimeTechnical . '">' . $datetimeHuman . '</time></p>
        </div>
    <a/>
</div>';
}

function createProjectsListing($projectsArray) {
    foreach ($projectsArray as $projectRecord) {
        $category = $projectRecord['category'] ?? '';
        $slug = $projectRecord['slug'] ?? '';
        echo generateProjectCard(
            $category,
            $slug,
            $projectRecord['title'] ?? '',
            $projectRecord['description'] ?? '',
            getProjectThumbnailLink($category,$slug),
            $projectRecord['username'] ?? '',
            $projectRecord['modified'] ?? ''
        );
    }
}

function generateCategoryLinks() {
    $categories = getCategories();
    if ($categories === false) {
        return;
    }
    foreach ($categories as $categoryRecord) {
        echo '<a href="/~dobiapa2/projects/' . ($categoryRecord['id'] ?? '') . '">' . ($categoryRecord['name'] ?? '') . '</a>';
    }
}

?>
<main>
    <?php if ($viewerUsername !== ''): ?>
    <div>
        <a href="/~dobiapa2/upload-project">Create a new Project!</a>
    </div>
    <?php endif ; ?>
    <h1><?= $projectsPageTitle ?></h1>
    <h2>Category Links</h2>
    <div class="category-link-container">
        <?php
            generateCategoryLinks();
        ?>
    </div>
    </div>
    <h2>List of projects</h2>
    <div class="page-form">
        <form method="get">
            <div>
                <label for="select-page-size">Projects per page:</label>
                <select id="select-page-size" name="size">
                    <?php
                    foreach ($defaultSizes as $someSize) {
                        echo '<option value="' . $someSize . '" ' . ($someSize == $currentSize ? 'selected>' : '>') . $someSize . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="select-page-sort">Sort by:</label>
                <select id="select-page-sort" name="sort">
                    <option value="title" <?= $currentSort === 'title' ? 'selected' : '' ?>>Title</option>
                    <option value="modified" <?= $currentSort == 'modified' ? 'selected' : '' ?>>Modified
                    </option>
                    <option value="created" <?= $currentSort === 'created' ? 'selected' : '' ?>>Created</option>
                </select>
            </div>
            <div>
                <label for="select-page-order">Sort order:</label>
                <select id="select-page-order" name="order">
                    <option value="asc" <?= $currentOrder === 'asc' ? 'selected' : '' ?>>Ascending</option>
                    <option value="desc" <?= $currentOrder === 'desc' ? 'selected' : '' ?>>Descending</option>
                </select>
            </div>
            <input type="submit" value="Apply">
        </form>
    </div>
    <div id="projects-section">
        <?php
            createProjectsListing($projectsList);
        ?>
    </div>
    <div class="page-controls">
        <?php
            generatePageControls($currentPageNumber,$lastPageNumber)
        ?>
    </div>
</main>