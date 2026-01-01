<?php
$viewState = ViewData::getInstance();

$username = $viewState->get('username');
$accountAge = $viewState->get('profile-age');
$roleName = $viewState->get('profile-role');

$prefillDisplayName = htmlspecialchars($viewState->get('profile-form-display-name'));
$prefillEmail = htmlspecialchars($viewState->get('profile-form-email'));
$prefillSocialsWebsite = htmlspecialchars($viewState->get('profile-form-socials-website'));

$prefillSocialsReddit = htmlspecialchars($viewState->get('profile-form-socials-reddit'));
$prefillSocialsTwitter = htmlspecialchars($viewState->get('profile-form-socials-twitter'));
$prefillSocialsInstagram = htmlspecialchars($viewState->get('profile-form-socials-instagram'));
$prefillSocialsDiscord = htmlspecialchars($viewState->get('profile-form-socials-discord'));

$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <form method="post" enctype="multipart/form-data" action="">
        <h1>Your User Profile</h1>
        <div class="profile-part">
            <div class="picture-container field">
                <label for="input-profile-picture">Profile Picture:</label>
                <button class="picture-container">
                    <img src="/~dobiapa2/api/internal/users/profile-picture.php?username=<?= $username ?>" alt="Your Profile Picture" id="profile-picture-image">
                </button>
                <input type="file" name="profile-picture" id="input-profile-picture">
            </div>
            <div class="stats-container field">
                Your username is <span class="monospace"><?= $username ?></span>.<br>
                Your account is <span class="bold"><?= $accountAge ?></span> old.<br>
                Your role is <span class="bold"><?= $roleName ?></span>.<br>
                <a href="/~dobiapa2/users/<?= $username ?>">View all your projects.</a>
            </div>
        </div>
        <p>Tell us more about yourself to enhance your public presence on our website.</p>
        <h2>Basic Details</h2>
        <div class="field">
            <label for="input-display-name">Display Name:</label>
            <input id="input-display-name" name="display-name" placeholder="<?= $username ?>" type="text"
                value="<?= $prefillDisplayName ?>" required>
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
                <input id="input-socials-twitter" name="socials-twitter" type="text"
                    value="<?= $prefillSocialsTwitter ?>">
            </div>
            <label for="input-socials-instagram">Instagram Username:</label>
            <div class="prefix-container">
                <label for="input-socials-instagram">instagram.com/</label>
                <input id="input-socials-instagram" name="socials-instagram" type="text"
                    value="<?= $prefillSocialsInstagram ?>">
            </div>
            <label for="input-socials-discord">Discord Server Invite:</label>
            <div class="prefix-container">
                <label for="input-socials-discord">discord.gg/</label>
                <input id="input-socials-discord" name="socials-discord" type="text"
                    value="<?= $prefillSocialsDiscord ?>">
            </div>
        </div>
        <h2>Change Password</h2>
        <div class="field">
            <p>You can change your password here. Make sure it's at least 8 characters long. Contains at least a single
                number, uppercase letter and lowercase letter.</p>
            <label for="input-password">Current Password:</label>
            <input id="input-password" name="password" type="password" value="">
            <label for="input-password-new">New Password:</label>
            <input id="input-password-new" name="password-new" type="password" value="">
            <label for="input-password-confirm">Confirm New Password:</label>
            <input id="input-password-confirm" name="password-confirm" type="password" value="">
        </div>
        <div>
            <input type="submit" value="Save Changes">
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
    </form>
</main>