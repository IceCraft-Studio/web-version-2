<?php
$viewState = ViewData::getInstance();

$userUsername = htmlspecialchars($viewState->get('page-username', ''));
$userDisplayName = htmlspecialchars($viewState->get('page-display-name', ''));
$userPicture = $viewState->get('page-picture-link', '');
$socialWebsite = htmlspecialchars($viewState->get('page-social-website', ''));
$socialReddit = htmlspecialchars($viewState->get('page-social-reddit', ''));
$socialTwitter = htmlspecialchars($viewState->get('page-social-twitter', ''));
$socialInstagram = htmlspecialchars($viewState->get('page-social-instagram', ''));
$socialDiscord = htmlspecialchars($viewState->get('page-social-discord', ''));

$viewerIsAdmin = $viewState->get('viewer-admin', false);



?>
<main>
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
    <?php if ($viewerIsAdmin): ?>
        <div id="admin-panel">
            <form method="post">
                <div>
                    <label>Manage User Action:</label>
                    <select>
                        <option value="" selected>
                            No Action
                        </option>
                        <option value="ban">
                            Ban User
                        </option>
                        <option value="promote-admin">
                            Promote User to Administrator
                        </option>
                        <option value="clear-name">
                            Clear Display Name
                        </option>
                        <option value="delete">
                            Delete User
                        </option>
                    </select>
                </div>
                <div>
                    <h2>Change Password</h2>
                
                </div>
            </form>
        </div>
    <?php endif; ?>
    <div id="projects-section">
        <h2>Their Projects</h2>
        
    </div>
</main>