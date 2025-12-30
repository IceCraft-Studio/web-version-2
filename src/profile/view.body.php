<?php
$viewState = ViewData::getInstance();

$username =     htmlspecialchars($viewState->get('username'));
$accountAge =   htmlspecialchars($viewState->get('profile-age'));
$role =         htmlspecialchars($viewState->get('profile-role'));

$prefillDisplayName     = htmlspecialchars($viewState->get('profile-form-display-name'));
$prefillEmail           = htmlspecialchars($viewState->get('profile-form-email'));
$prefillSocialsWebsite  = htmlspecialchars($viewState->get('profile-form-socials-website'));

$prefillSocialsReddit       = htmlspecialchars($viewState->get('profile-form-socials-reddit'));
$prefillSocialsTwitter      = htmlspecialchars($viewState->get('profile-form-socials-twitter'));
$prefillSocialsInstagram    = htmlspecialchars($viewState->get('profile-form-socials-instagram'));
$prefillSocialsDiscord      = htmlspecialchars($viewState->get('profile-form-socials-discord'));

$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <h1>Your User Profile</h1>
    <div>
        Your username is <span class="monospace"><?= $username ?></span>.
        Your account is <span class="bold"><?= $accountAge ?></span> old.
        Your role is <span class="bold"><?= $role ?></span>
    </div>
    <a>
        View all your projects.
    </a href="/~dobiapa2/users/<?= $username ?>">
    <form enctype="multi">
        <h2>Basic Details</h2>
        <div class="field">
            <label for="input-display-name" >Display Name:</label>
            <input id="input-display-name" name="display-name" type="text" value="<?= $prefillDisplayName ?>" required>
            <label for="input-email">E-mail Address:</label>
            <input id="input-email" name="email" type="text" value="<?= $prefillEmail ?>">
            <label for="input-socials-website">Personal Website:</label>
            <input id="input-socials-website" name="socials-website" type="text" value="<?= $prefillSocialsWebsite ?>">
        </div>
        <h2>Social Links</h2>
        <div class="field">
            <label for="input-socials-reddit">Reddit Username:</label>
            <div class="prefix-container">
                <label for="input-socials-reddit">reddit.com/user/</label>
                <input id="input-socials-reddit" name="socials-reddit" type="text" value="<?= $prefillSocialsReddit ?>">
            </div>
            <label for="input-socials-twitter">Twitter Username:</label>
            <div class="prefix-container">
                <label for="input-socials-twitter">x.com/</label>
                <input id="input-socials-twitter" name="socials-twitter" type="text" value="<?= $prefillSocialsTwitter ?>">
            </div>
            <label for="input-socials-instagram">Instagram Username:</label>
            <div class="prefix-container">
                <label for="input-socials-instagram">instagram.com/</label>
                <input id="input-socials-instagram" name="socials-instagram" type="text" value="<?= $prefillSocialsInstagram ?>">
            </div>
            <label for="input-socials-discord">Discord Server Invite:</label>
            <div class="prefix-container">
                <label for="input-socials-discord">discord.gg/</label>
                <input id="input-socials-discord" name="socials-discord" type="text" value="<?= $prefillSocialsDiscord ?>">
            </div>
        </div>
        <h2>Change Password</h2>
        <div class="field">
            <label for="input-password">Current Password:</label>
            <input id="input-password" name="password" type="password" value="">
            <label for="input-password-new">New Password:</label>
            <input id="input-password-new" name="password-new" type="password" value="">
            <label for="input-password-confirm">Confirm New Password:</label>
            <input id="input-password-confirm" name="password-confirm" type="password" value="">
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
        <input type="submit" value="Edit Profile">
    </form>
</main>