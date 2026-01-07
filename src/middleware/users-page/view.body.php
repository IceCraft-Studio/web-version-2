<?php
$viewState = ViewData::getInstance();

$userUsername = $viewState->get('page-username');
$userPicture = $viewState->get('page-profile-picture');
$socialWebsite = $viewState->get('page-social-website');
$socialReddit = $viewState->get('page-social-reddit');
$socialTwitter = $viewState->get('page-social-twitter');
$socialInstagram = $viewState->get('page-social-instagram');
$socialDiscord = $viewState->get('page-social-discord');

$viewerIsAdmin = $viewState->get('viewer-admin', false);



?>
<main>
    <div id="profile-section">
        <div>
            <img src="">
            <h1></h1>
        </div>
            <?php if ($socialWebsite != ''): ?>
            <a href="<?= $socialWebsite ?>"></a>
            <?php endif; ?>
            <?php if ($socialReddit != ''): ?>
            <a href="https://reddit.com/user/<?= $socialReddit ?>"></a>
            <?php endif; ?>
            <?php if ($socialTwitter != ''): ?>
            <a href="https://x.com/<?= $socialTwitter ?>"></a>
            <?php endif; ?>
            <?php if ($socialInstagram != ''): ?>
            <a href="https://instagram.com/<?= $socialInstagram ?>"></a>
            <?php endif; ?>
            <?php if ($socialDiscord != ''): ?>
            <a href="https://discord.gg/<?= $socialDiscord ?>"></a>
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