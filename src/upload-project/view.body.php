<?php
$viewState = ViewData::getInstance();

$projectUploadState = $viewState->get('upload-project-state', ProjectUploadState::NoState);

$prefillEditing = $viewState->get('form-editing', '0');
$prefillTitle = htmlspecialchars($viewState->get('form-title', ''));
$prefillDescription = htmlspecialchars($viewState->get('form-description', ''));
$prefillSlug = htmlspecialchars($viewState->get('form-slug', ''));
$prefillCategory = htmlspecialchars($viewState->get('form-category', ''));
$prefillMarkdown = htmlspecialchars($viewState->get('form-markdown-article', ''));

$previousGalleryArray = $viewState->get('form-previous-gallery');
$previousLinksArray = $viewState->get('form-previous-links');
$previousFilesArray = $viewState->get('form-previous-files');

$username = $viewState->get('verified-username', '');
$displayName = $viewState->get('verified-display-name', '');
$profilePicture = $viewState->get('verified-profile-picture', '');
$userLink = getUserLink($username);

if ($prefillEditing === '1') {
    $cardThumbnailSrc = '/~dobiapa2/api/internal/projects/thumbnail.php?variant=preview&category=' . $prefillCategory . '&project=' . $prefillSlug;
} else {
    $cardThumbnailSrc = '/~dobiapa2/assets/empty-thumbnail.webp';
}

// State Processing When returning incorrectly filled form 
$showErrorBanner = true;
$errorBannerMessage = '';

switch ($projectUploadState) {
    case ProjectUploadState::NoState:
        $showErrorBanner = false;
        break;
    case ProjectUploadState::CsrfInvalid:
        $errorBannerMessage = 'Critical Client Error! Please try resending the form.';
        break;
    case ProjectUploadState::TitleInvalid:
        $errorBannerMessage = 'Upload failed! Title is invalid. Make sure it is between 6 and 96 characters long (inclusive).';
        break;
    case ProjectUploadState::DescriptionInvalid:
        $errorBannerMessage = 'Upload failed! Description is invalid. Make sure it is between 34 and 320 characters long (inclusive).';
        break;
    case ProjectUploadState::ThumbnailInvalid:
        $errorBannerMessage = 'Upload failed! Thumbnail is invalid. Make sure its aspect ratio is 16:9 and its file size is not bigger than 15 MB.';
        break;
    case ProjectUploadState::SlugInvalid:
        $errorBannerMessage = 'Upload failed! Slug is invalid. Make sure it is between 6 and 64 characters long (inclusive).';
        break;
    case ProjectUploadState::SlugTaken:
        $errorBannerMessage = 'Upload failed! This slug is taken, please try another one.';
        break;
    case ProjectUploadState::CategoryInvalid:
        $errorBannerMessage = 'Upload failed! Select a category!';
        break;
    case ProjectUploadState::ArticleInvalid:
        $errorBannerMessage = 'Upload failed! Article is invalid. Make sure it is between 128 and 6144 characters long (inclusive).';
        break;
    case ProjectUploadState::GalleryInvalid:
        $errorBannerMessage = 'Upload failed! Some gallery image is invalid. Make sure their aspect ratio is 16:9 and their file size is not bigger than 15 MB.';
        break;
    case ProjectUploadState::LinkInvalid:
        $errorBannerMessage = 'Upload failed! Link invalid. Make sure its URL is valid, uses HTTP or HTTPS and is not longer than 200 characters and its display name is not longer than 96 characters.';
        break;
    case ProjectUploadState::FileInvalid:
        $errorBannerMessage = 'Upload failed! File upload invalid. Make sure its file size is not bigger than 30 MB and its display name is not longer than 96 characters.';
        break;
    case ProjectUploadState::NoUploads:
        $errorBannerMessage = 'Upload failed! Make sure to provide at least one link or file upload with your project.';
        break;
    case ProjectUploadState::ServerError:
        $errorBannerMessage = 'Upload failed! Crtical Server Error! Please try again later.';
        break;
    case ProjectUploadState::Success:
        $showErrorBanner = false;
        break;
}

function generateGalleryItem($i, $galleryLink, $fileName)
{
    return '
<li data-old-gallery-index="' . $i . '" class="gallery-image edit-inserted">
    <span class="image-title">Previous Image #' . $i + 1 . '</span>
    <button class="gallery-container">
        <img src="' . $galleryLink . '" alt="Previous Image #' . $i + 1 . '">
    </button>
    <button class="delete-item" data-index="' . $i . '">
        <img src="/~dobiapa2/assets/icons/bin.svg">
        <span>Delete Image</span>
    </button>
    <input name="gallery-delete-name[' . $i . ']" value="' . $fileName . '" type="hidden" disabled>
</li>';
}

function generateFileItem($i, $fileLink, $displayName, $fileName)
{
	return '
<li data-old-file-index="' . $i . '" class="edit-inserted">
	<div class="field">
		<p>Previous File #' . $i + 1 . ' to download the project.</p>
        <a href="' . $fileLink . '">Download the old file upload #' . $i + 1 . '.</a>
        <label for="old-input-file-name-' . $i . '">Display Name:</label>
        <input type="text" name="old-file-name[' . $i . ']" id="old-input-file-name-' . $i . '" value="' .  $displayName . '" readonly>
        <button class="delete-item" data-index="' . $i . '">
            <img src="/~dobiapa2/assets/icons/bin.svg">
        <span>Delete File</span>
        </button>
        <input name="file-delete-name[' . $i . ']" value="' . $fileName . '" type="hidden" disabled>
    </div>
</li>';
}


function generateLinkItem($i, $urlLink, $displayName) {
	return '
<li data-old-link-index="' . $i . '" class="edit-inserted">
    <div class="field">
        <p>Previous Link #' . $i + 1 . ' to download the project.</p>
        <label for="old-input-link-url-' . $i . '">URL:</label>
        <input type="text" name="old-link-url[' . $i . ']" id="old-input-link-url-' . $i . '" value="' . $urlLink . '" readonly>
        <label for="old-input-link-name-' . $i . '">Display Name:</label>
        <input type="text" name="old-link-name[' . $i . ']" id="old-input-link-name-' . $i . '" value="' . $displayName . '" readonly>
        <button class="delete-item" data-index="' . $i . '">
            <img src="/~dobiapa2/assets/icons/bin.svg">
        <span>Delete Link</span>
        </button>
        <input name="link-delete-url[' . $i . ']" value="' . htmlspecialchars($urlLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" type="hidden" disabled>
    </div>
</li>';
}

function createPreviousGallery($galleryArray) {
    if (!is_array($galleryArray)) {
        return;
    }
    $index = 0;
    foreach ($galleryArray as $galleryRecord) {
        echo generateGalleryItem(
            $index,
            htmlspecialchars($galleryRecord['link'] ?? ''),
            htmlspecialchars($galleryRecord['file_name'] ?? '')
        );
        $index++;
    }
}

function createPreviousFileUploads($fileArray) {
    if (!is_array($fileArray)) {
        return;
    }
    $index = 0;
    foreach ($fileArray as $fileRecord) {
        echo generateFileItem(
            $index,
            htmlspecialchars($fileRecord['link'] ?? ''),
            htmlspecialchars($fileRecord['display_name'] ?? ''),
            htmlspecialchars($fileRecord['file_name'] ?? '')
        );
        $index++;
    }
}

function createPreviousLinks($linkArray) {
    if (!is_array($linkArray)) {
        return;
    }
    $index = 0;
    foreach ($linkArray as $linkRecord) {
        echo generateLinkItem(
            $index,
            htmlspecialchars($linkRecord['url'] ?? ''),
            htmlspecialchars($linkRecord['display_name'] ?? '')
        );
        $index++;
    }
}

function createCategorySelection($prefillCategory) {
    $categories = getCategories() ?: [];
    $savedCategories = [
        '<option value="" selected></option>'
    ];
    foreach ($categories as $categoryRecord) {
    if ($prefillCategory == $categoryRecord['id']) {
        $selectedString = 'selected';
        $savedCategories[0] = '';
    } else {
        $selectedString = '';
    }
    $savedCategories[] =  '<option value="' . $categoryRecord['id'] . '" ' . $selectedString  . '>' . $categoryRecord['name'] . '</option>';
    }
    foreach ($savedCategories as $savedCategory) {
        echo $savedCategory;
    }

}

// Get session csrf-token
$csrfToken = getCsrf('upload-project');
?>
<main>
    <?php if ($showErrorBanner): ?>
        <div class="update-banner fail">
            <?= $errorBannerMessage ?>
        </div>
    <?php endif ; ?>
    <h1><?= $prefillEditing === '1' ? 'Edit exisiting project' : 'Create a new project' ?></h1>
    <div>
        Fields marked with <span class="color-required">*</span> are required!
    </div>
    <form method="post" enctype="multipart/form-data">
        <h2>Introduction</h2>
        <div class="introduction-part">
            <div class="introduction-details">
                <div class="field">
                    <label for="input-title" class="<?= $projectUploadState === ProjectUploadState::TitleInvalid ?  'color-required' : '' ?>">
                        Title of your project:
                    </label>
                    <input id="input-title" name="title" type="text" value="<?= $prefillTitle ?>" minlength="6"
                        maxlength="96" required>
                </div>
                <div class="field">
                    <label for="input-description" class="<?= $projectUploadState === ProjectUploadState::DescriptionInvalid ? 'color-required' : '' ?>">
                        Brief description:
                    </label>
                    <textarea id="input-description" name="description" type="text" minlength="24" maxlength="320"
                        required><?= $prefillDescription ?></textarea>
                </div>
                <div class="field">
                    <label for="input-thumbnail">Thumbnail:</label>
                    <div class="<?= $projectUploadState === ProjectUploadState::ThumbnailInvalid ? 'color-required' : '' ?>">
                        <p class="thumbnail-size">The image must be JPEG, PNG or WEBP of 15MB at most!</p>
                        <p class="thumbnail-ratio">The image needs to have 16:9 aspect ratio!</p>
                    </div>
                    <input id="input-thumbnail" name="thumbnail" type="file" accept=".jpeg,.jpg,.png,.webp"
                        <?= $prefillEditing === '1' ? '' : 'required' ?>></input>
                </div>
            </div>
            <div class="introduction-preview">
                <div>
                    <div class="project-card">
                        <div class="user-part">
                            <a href="<?= $userLink ?>" target="_blank">
                                <img src="<?= $profilePicture ?>">
                                <span>
                                    <?= $displayName ?>
                                </span>
                            </a>
                        </div>
                        <div class="project-part">
                            <img src="<?= $cardThumbnailSrc ?>" alt="Project Card Thumbnail">
                            <h3 title="<?= $prefillTitle ?>"><?= $prefillTitle ?></h3>
                            <p class="description" title="<?= $prefillDescription ?>"><?= $prefillDescription ?></p>
                            <p class="modified">Date Modified: <time
                                    datetime="<?= date('Y-m-d') ?>"><?= date('d/m/Y') ?></time></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h2>Project Details</h2>
        <div class="details-part">
            <div class="field slug-editor">
                <label for="input-slug">Unique part of the URL for your project:</label>
                <div class="prefix-container">
                    <label for="input-slug">/<?= $prefillCategory == '' ? 'category' : $prefillCategory ?>/</label>
                    <input id="input-slug" name="slug" type="text" value="<?= $prefillSlug ?>" minlength="6"
                        maxlength="64" readonly required>
                </div>
                <div class="hint <?= $projectUploadState === ProjectUploadState::SlugInvalid ? 'color-required' : '' ?>">
                    The slug must be between 6 and 96 characters long and may only contain numbers,
                    lowercase letters and single hyphens between words. 
                    <span id="slug-taken" class="<?= $projectUploadState === ProjectUploadState::SlugTaken ? '' : 'hidden' ?> color-required inline-block">
                        This slug is taken, please try another one.
                    </span>
                </div>
            </div>
            <div class="field category-selection">
                <label for="input-category">Category:</label>
                <select id="input-category" name="category" <?= $prefillEditing == '1' ? 'disabled' : '' ?> required>
                    <?php
                        createCategorySelection($prefillCategory);
                    ?>
                </select>
                <?php if ($prefillEditing == '1'): ?>
                <input type="hidden" name="category" value="<?= $prefillCategory ?>">
                <?php endif ; ?>
                <div class="hint <?= $projectUploadState === ProjectUploadState::CategoryInvalid ? 'color-required' : '' ?>">
                    The category must be selected before you can type in the slug.
                </div>
            </div>
        </div>
        <h2>Article Contents</h2>
        <div class="article-part">
            <div class="article-editor field">
                <label for="md-input" class="<?= $projectUploadState === ProjectUploadState::ArticleInvalid ? 'color-required' : '' ?>">
                    Your project's article, use <a href="https://github.github.com/gfm/"
                        title="GFM Spec" target="_blank">GitHub Flavored Markdown</a>:</label>
                <div class="switch-buttons">
                    <button id="btn-edit-article" disabled>Edit</button>
                    <button id="btn-preview-article">Preview</button>
                </div>
                <textarea id="md-input" name="markdown-article" minlength="128" maxlength="6144"
                    required><?= $prefillMarkdown ?></textarea>
                <div id="md-preview" class="hidden"></div>
            </div>
            <div class="article-gallery">
                <div class="gallery-info hint">
                    You can upload up to 12 images to put inside your article. Click them to get
                    their link to use them in markdown of your article.
                </div>
                <ul id="gallery-preview">
                    <?php
                        createPreviousGallery($previousGalleryArray);
                    ?>
                    <!-- This content is generated by a JS script!  -->
                </ul>
            </div>
        </div>
        <h2>Downloads & Links</h2>
        <div class="field">
            <div>
                The project needs to provide at least 1 file or link and at most 5 of each for the audience to download.
            </div>
            <div>
                Max file upload size is 30MB. File names are preserved but modified to be safe. Make sure the URL is valid HTTP(S) and not longer than 200 charactetrs.
            </div>
            <div>
                Display name can be up to 96 characters long. 
            </div>
        </div>
        <div class="downloads-part">
            <div class="field">
                <h3>Add a link</h3>
                <ul id="link-adder" class="adder">
                    <?php
                        createPreviousLinks($previousLinksArray);
                    ?>
                    <li>
                        <button class="add-another" title="Add another link to your project.">
                            <div>
                                + Add Link
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="field">
                <h3>Upload a file</h3>
                <ul id="file-adder" class="adder">
                    <?php
                        createPreviousFileUploads($previousFilesArray);
                    ?>
                    <li>
                        <button class="add-another" title="Add another file to your project.">
                            <div>
                                + Add File
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
            <input type="submit" value="<?= $prefillEditing === '1' ? 'Edit Project' : 'Create Project' ?>">
        </div>
        <input type="hidden" name="editing" value="<?= $prefillEditing ?>">
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
    </form>
</main>