<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/html-gen.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/paging.php';

$viewState = ViewData::getInstance();

$viewerUsername = $viewState->get('verified-username','');

// Project Data
$projectsList = $viewState->get('projects-list',[]);
$projectsCategory = $viewState->get('projects-category','');
$projectsCategoryName = $viewState->get('projects-category-name','');

$projectsPageTitle = 'All Projects';
if ($projectsCategory != '') {
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

?>
<main>
    <?php if ($viewerUsername != ''): ?>
    <div class="create-project-button">
        <a href="/~dobiapa2/upload-project">Create a new Project!</a>
    </div>
    <?php endif ; ?>
    <h1><?= $projectsPageTitle ?></h1>
    <h2>Category Links</h2>
    <div class="category-link-container">
        <?php
            createCategoryLinks();
        ?>
    </div>
    <h2>List of projects</h2>
    <p>
        Page <?= $currentPageNumber ?> of <?= $lastPageNumber ?>
    </p>
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
            <div>
                <label for="input-page-number">Page Number:</label>
                <input type="number" id="input-page-number" name="page" value="1" min="1" max="<?= $lastPageNumber ?>">
            </div>
            <input type="submit" value="Apply">
        </form>
    </div>
    <div id="projects-section">
        <?php
            createProjectsListing($projectsList,'<p> Empty page. </p>');
        ?>
    </div>
    <div class="page-controls">
        <?php
            generatePageControls($currentPageNumber,$lastPageNumber)
        ?>
    </div>
</main>