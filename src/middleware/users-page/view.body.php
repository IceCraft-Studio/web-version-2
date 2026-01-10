<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/html-gen.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/paging.php';

$viewState = ViewData::getInstance();

// User Data
$userUsername = htmlspecialchars($viewState->get('page-username', ''));
$userDisplayName = htmlspecialchars($viewState->get('page-display-name', ''));
$userRole = htmlspecialchars($viewState->get('page-user-role', ''));
$userIsAdmin = $viewState->get('page-user-admin', false);
$userPicture = $viewState->get('page-picture-link', '');
$socialWebsite = htmlspecialchars($viewState->get('page-social-website', ''));
$socialReddit = htmlspecialchars($viewState->get('page-social-reddit', ''));
$socialTwitter = htmlspecialchars($viewState->get('page-social-twitter', ''));
$socialInstagram = htmlspecialchars($viewState->get('page-social-instagram', ''));
$socialDiscord = htmlspecialchars($viewState->get('page-social-discord', ''));

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
$viewerIsAdmin = $viewState->get('viewer-admin', false);

$showActionBanner = false;
$showPasswordBanner = false;

?>
<main>
    <?php if ($showActionBanner): ?>
    <div class="update-banner <?= $successAction ? 'success' : 'fail'?>">
        <?= $actionMessage ?>
    </div>
    <?php endif; ?>
    <?php if ($showPasswordBanner): ?>
    <div class="update-banner <?= $successPassword ? 'success' : 'fail'?>">
        <?= $passwordMessage ?>
    </div>
    <?php endif; ?>
    <div id="profile-section">
        <div>
            <img src="<?= $userPicture ?>">
            <h1><?= $userDisplayName ?></h1>
        </div>
        <div class="social-links">
            <?php if ($socialWebsite != ''): ?>
            <a href="<?= $socialWebsite ?>" title="Website"><img src="/~dobiapa2/assets/icons/home.svg"> My Personal Website</a>
            <?php endif; ?>
            <?php if ($socialReddit != ''): ?>
            <a href="https://reddit.com/user/<?= $socialReddit ?>" title="Reddit"><img src="/~dobiapa2/assets/icons/socials/reddit.svg"> My Reddit Profile</a>
            <?php endif; ?>
            <?php if ($socialTwitter != ''): ?>
            <a href="https://x.com/<?= $socialTwitter ?>" title="Twitter"><img src="/~dobiapa2/assets/icons/socials/twitter.svg"> My Twitter Profile</a>
            <?php endif; ?>
            <?php if ($socialInstagram != ''): ?>
            <a href="https://instagram.com/<?= $socialInstagram ?>" title="Instagram"><img src="/~dobiapa2/assets/icons/socials/instagram.svg"> My Instagram Profile</a>
            <?php endif; ?>
            <?php if ($socialDiscord != ''): ?>
            <a href="https://discord.gg/<?= $socialDiscord ?>" title="Discord"><img src="/~dobiapa2/assets/icons/socials/discord.svg"> My Discord Server</a>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($viewerIsAdmin && !$userIsAdmin): ?>
        <div id="admin-panel">
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
                    <div>
                        <label for="input-password-new">New Password:</label>
                        <input id="input-password-new" name="password-new" type="password" value="">
                    </div>
                    <div>
                        <label for="input-password-confirm">Confirm New Password:</label>
                        <input id="input-password-confirm" name="password-confirm" type="password" value="">
                    </div>
                </div>
                <input type="submit" value="Run Action">
            </form>
        </div>
    <?php endif; ?>
    <h2>Their Projects</h2>
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
            createProjectsListing($projectsList);
        ?>
    </div>
    <div class="page-controls">
        <?php
            generatePageControls($currentPageNumber,$lastPageNumber)
        ?>
    </div>
</main>