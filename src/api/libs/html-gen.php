<?php

/**
 * Generates a common HTML for a project card.
 * @param string $category The category of the project.
 * @param string $slug The slug of the project.
 * @param string $title The title of the project.
 * @param string $description The description of the project.
 * @param string $username Username of the creator.
 * @param string $modified The date modified of the project.
 * @return string HTML text of the project card.
 */
function generateProjectCard($category,$slug,$title,$description,$username,$modified) {
    $projectLink = getProjectLink($category,$slug);
    $thumbnailLink = getProjectThumbnailLink($category,$slug);
    $userData = getUserData($username);
    if (($userData['display_name'] ?? '') == '') {
        $displayName = $username;
    } else {
        $displayName = htmlspecialchars($userData['display_name']);
    }
    $userLink = getUserLink($username);
    $userPictureLink = getUserPictureLink($username);

    $datetimeTechnical = date("Y-m-d\TH:i",strtotime($modified));
    $datetimeHuman = date("d/m/Y H:i",strtotime($modified));

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
            <img src="' . $thumbnailLink . '" alt="Project Card Thumbnail">
            <h3 title="' . htmlspecialchars($title) . '">' . htmlspecialchars($title) . '</h3>
            <p class="description" title="' . htmlspecialchars($description) . '">' . htmlspecialchars($description) . '</p>
            <p class="modified">Date Modified: <time datetime="' . $datetimeTechnical . '">' . $datetimeHuman . '</time></p>
        </div>
    </a>
</div>';
}

/**
 * `echo`'s project card HTML of all projects in the array.
 * @param array $projectsArray Array of project data from the database.
 * @param string $noItemsMessage Present this string when the array is empty. (Optional.)
 * @return void
 */
function createProjectsListing($projectsArray, $noItemsMessage = '') {
    foreach ($projectsArray as $projectRecord) {
        $category = $projectRecord['category'] ?? '';
        $slug = $projectRecord['slug'] ?? '';
        echo generateProjectCard(
            $category,
            $slug,
            $projectRecord['title'] ?? '',
            $projectRecord['description'] ?? '',
            $projectRecord['username'] ?? '',
            $projectRecord['datetime_modified'] ?? ''
        );
    }
    if (count($projectsArray) === 0) {
        echo $noItemsMessage;
    }
}

/**
 * `echo`'s links to all categories that exist in the database.
 * @return void
 */
function createCategoryLinks() {
    $categories = getCategories();
    if ($categories === false) {
        return;
    }
    foreach ($categories as $categoryRecord) {
        echo '<a href="/~dobiapa2/projects/' . ($categoryRecord['id'] ?? '') . '">' . ($categoryRecord['name'] ?? '') . '</a>';
    }
}