<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/html-gen.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/paging.php';

$viewState = ViewData::getInstance();

// User Data
$userUsername = htmlspecialchars($viewState->get('page-username', ''));
$userDisplayName = htmlspecialchars($viewState->get('page-display-name', ''));
$userRole = htmlspecialchars($viewState->get('page-user-role', ''));
$userCreated = $viewState->get('page-user-created','');
$userPicture = $viewState->get('page-picture-link', '');
$socialWebsite = htmlspecialchars($viewState->get('page-social-website', ''));
$socialReddit = htmlspecialchars($viewState->get('page-social-reddit', ''));
$socialTwitter = htmlspecialchars($viewState->get('page-social-twitter', ''));
$socialInstagram = htmlspecialchars($viewState->get('page-social-instagram', ''));
$socialDiscord = htmlspecialchars($viewState->get('page-social-discord', ''));
$projectsList = $viewState->get('projects-list', []);

$createdTechnical = date("Y-m-d\TH:i",strtotime($userCreated));
$createdHuman = date("M j, Y",strtotime($userCreated));

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

// Admin Data
$userIsAdmin = $viewState->get('page-user-admin', false);
$viewerIsAdmin = $viewState->get('viewer-admin', false);
$viewerIsOwner = $viewState->get('viewer-owner', false);

$adminManageState = $viewState->get('user-manage-state', UserActionState::NoUpdate);
$adminPasswordState = $viewState->get('user-password-state', ManagePasswordState::NoUpdate);

$showActionBanner = true;
$showPasswordBanner = true;

$passwordMessage = '';
$actionMessage = '';

$passwordSuccess = false;
$actionSuccess = false;

switch ($adminPasswordState) {
    case ManagePasswordState::NoUpdate:
        $showPasswordBanner = false;
        break;
    case ManagePasswordState::PasswordInvalid:
        $passwordMessage = 'Password update failed! Make sure it is at least 8 characters long and contains at least a single number, uppercase letter and lowercase letter.';
        break;
    case ManagePasswordState::PasswordMismatch:
        $passwordMessage = 'Password update failed! Passwords do not match.';
        break;
    case ManagePasswordState::Failure:
        $passwordMessage = 'Password update failed! Server error.';
        break;
    case ManagePasswordState::Success:
        $passwordMessage = 'Password updated successfully!.';
        $passwordSuccess = true;
        break;
}

switch ($adminManageState) {
    case UserActionState::NoUpdate:
        $showActionBanner = false;
        break;
    case UserActionState::CsrfInvalid:
        $actionMessage = 'Critical client error! Please try resending the form.';
        break;
    case UserActionState::Failure:
        $actionMessage = 'Requested action has failed.';
        break;
    case UserActionState::Success:
        $actionMessage = 'Requested action was successful.';
        $actionSuccess = true;
        break;
}

$csrfToken = '';
if ((($viewerIsAdmin && !$userIsAdmin) || $viewerIsOwner)) {
    $csrfToken = getCsrf('users-page');
}

?>
<main>
    <?php if ($showActionBanner): ?>
    <div class="update-banner <?= $actionSuccess ? 'success' : 'fail'?>">
        <?= $actionMessage ?>
    </div>
    <?php endif; ?>
    <?php if ($showPasswordBanner): ?>
    <div class="update-banner <?= $passwordSuccess ? 'success' : 'fail'?>">
        <?= $passwordMessage ?>
    </div>
    <?php endif; ?>
    <div id="profile-section">
        <div class="user-header">
            <img src="<?= $userPicture ?>" alt="<?= $userDisplayName ?>">
            <h1><?= $userDisplayName ?></h1>
            <div class="member-since">
                Member Since: <time datetime="<?= $createdTechnical ?>"><?= $createdHuman ?></time>
            </div>
        </div>
        <div class="social-links">
            <?php if ($socialWebsite != ''): ?>
            <a href="<?= $socialWebsite ?>" title="Website" target="_blank"><img src="/~dobiapa2/assets/icons/home.svg" alt="Home icon"> My Personal Website</a>
            <?php endif; ?>
            <?php if ($socialReddit != ''): ?>
            <a href="https://reddit.com/user/<?= $socialReddit ?>" title="Reddit" target="_blank"><img src="/~dobiapa2/assets/icons/socials/reddit.svg" alt="Reddit icon"> My Reddit Profile</a>
            <?php endif; ?>
            <?php if ($socialTwitter != ''): ?>
            <a href="https://x.com/<?= $socialTwitter ?>" title="Twitter" target="_blank"><img src="/~dobiapa2/assets/icons/socials/twitter.svg" alt="Twitter icon"> My Twitter Profile</a>
            <?php endif; ?>
            <?php if ($socialInstagram != ''): ?>
            <a href="https://instagram.com/<?= $socialInstagram ?>" title="Instagram" target="_blank"><img src="/~dobiapa2/assets/icons/socials/instagram.svg" alt="Instagram icon"> My Instagram Profile</a>
            <?php endif; ?>
            <?php if ($socialDiscord != ''): ?>
            <a href="https://discord.gg/<?= $socialDiscord ?>" title="Discord" target="_blank"><img src="/~dobiapa2/assets/icons/socials/discord.svg" alt="Discord icon"> My Discord Server</a>
            <?php endif; ?>
        </div>
    </div>
    <?php if (($viewerIsAdmin && !$userIsAdmin) || $viewerIsOwner): ?>
        <div id="admin-panel">
            <h2>User Management</h2>
            <form method="post">
                <div>
                    <label for="input-user-action">Manage User Action:</label>
                    <select name="user-action" id="input-user-action">
                        <option value="" selected>
                            No Action
                        </option>
                        <option value="ban-user">
                            <?= $userRole === UserRole::Banned->value ?  'Unban User' : 'Ban User' ?>
                        </option>
                        <option value="promote-admin">
                            Promote User to Administrator
                        </option>
                        <option value="clear-name">
                            Clear Display Name
                        </option>
                        <option value="delete-user">
                            Delete User (includes Projects)
                        </option>
                    </select>
                </div>
                <div>
                    <label for="input-password-new">New Password:</label>
                    <input id="input-password-new" name="password-new" type="password" value="">
                </div>
                <div>
                    <label for="input-password-confirm">Confirm New Password:</label>
                    <input id="input-password-confirm" name="password-confirm" type="password" value="">
                </div>
                <input type="submit" value="Run Action">
                <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
            </form>
        </div>
    <?php endif; ?>
    <h2>Their Projects</h2>
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
                    <option value="modified" <?= $currentSort === 'modified' ? 'selected' : '' ?>>Modified
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
            createProjectsListing($projectsList, '<p> This user hasn\'t uploaded any projects yet. </p>');
        ?>
    </div>
    <div class="page-controls">
        <?php
            generatePageControls($currentPageNumber,$lastPageNumber)
        ?>
    </div>
</main>