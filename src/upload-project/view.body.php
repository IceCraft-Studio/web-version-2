<?php
$viewState = ViewData::getInstance();

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
    $cardThumbnailSrc = '/~dobiapa2/api/internal/projects/thumbnail.php?variant=preview&category=' . $prefillCategory . '&slug=' . $prefillSlug;
} else {
    $cardThumbnailSrc = '/~dobiapa2/assets/empty-thumbnail.webp';
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
        <span>Delete Image</span>
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
        <span>Delete Image</span>
        </button>
        <input name="link-delete-url[' . $i . ']" value="' . $urlLink . '" type="hidden" disabled>
    </div>
</li>';
}

function createPreviousGallery($galleryArray) {
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

// Get session csrf-token
$csrfToken = getCsrf('upload-project');
?>
<main>
    <?php if ($showErrorBanner): ?>
        <div class="update-banner">

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
                    <label for="input-title">Title of your project:</label>
                    <input id="input-title" name="title" type="text" value="<?= $prefillTitle ?>" minlength="6"
                        maxlength="96" required>
                </div>
                <div class="field">
                    <label for="input-description">Brief description:</label>
                    <textarea id="input-description" name="description" type="text" minlength="24" maxlength="320"
                        required><?= $prefillDescription ?></textarea>
                </div>
                <div class="field">
                    <label for="input-thumbnail">Thumbnail:</label>
                    <div>
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
                    <label for="input-slug">/category/</label>
                    <input id="input-slug" name="slug" type="text" value="<?= $prefillSlug ?>" minlength="6"
                        maxlength="64" readonly required>
                </div>
                <div class="hint">The slug must be between 6 and 96 characters long and may only contain numbers,
                    lowercase letters and single hyphens between words. <span id="slug-taken"
                        class="hidden color-required inline-block">This slug is taken, please try another one.</span>
                </div>
            </div>
            <div class="field category-selection">
                <label for="input-category">Category:</label>
                <select id="input-category" name="category" value="<?= $prefillCategory ?>" <?= $prefillEditing === '1' ? 'readonly' : '' ?> required></select>
                <div class="hint">The category must be selected before you can type in the slug.</div>
            </div>
        </div>
        <h2>Article Contents</h2>
        <div class="article-part">
            <div class="article-editor field">
                <label for="md-input">Your project's article, use <a href="https://github.github.com/gfm/"
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
                    their link to use them in markdown of your article. Unused links will be ignored.
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
                Max file upload size is 30MB. File names are preserved but modified to be safe. Max URL length is 200 characters. 
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