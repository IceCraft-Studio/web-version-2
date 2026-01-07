<?php
const CLASS_SUCCESS = 'success';
const CLASS_FAIL = 'fail';

$viewState = ViewData::getInstance();

$showPictureUpdateBanner = true;
$showProfileUpdateBanner = true;
$showPasswordUpdateBanner = true;
$successPicture = false;
$successPassword = false;
$successProfile = false;

switch ($viewState->get('picture-update-state',PictureUpdateState::NoUpdate)) {
    case PictureUpdateState::NoUpdate:
        $showPictureUpdateBanner = false;
        break;
    case PictureUpdateState::WrongSize:
        $pictureMessage = 'Profile picture update failed! The image must be at most 15MB in size!';
        break;
    case PictureUpdateState::WrongType:
        $pictureMessage = 'Profile picture update failed! The image must be in PNG, JPEG or WEBP format!';
        break;
    case PictureUpdateState::WrongAspectRatio:
        $pictureMessage = 'Profile picture update failed! The image must have 1:1 aspect ratio!';
        break;
    case PictureUpdateState::Failure:
        $pictureMessage = 'Critical Server Error! Please try again later.';
        break;
    case PictureUpdateState::Success:
        $pictureMessage = 'Profile picture updated successfully!';
        $successPicture = true;
        break;
}

switch ($viewState->get('profile-update-state',ProfileUpdateState::NoUpdate)) {
    case ProfileUpdateState::NoUpdate:
        $showProfileUpdateBanner = false;
        break;
    case ProfileUpdateState::CsrfInvalid:
        $showPasswordUpdateBanner = false;
        $profileMessage = 'Critical Client Error! Please try resending the form.';
        break;
    case ProfileUpdateState::Failure:
        $profileMessage = $viewState->get('profile-update-fail-message','Critical Server Error! Please try again later.');
        break;
    case ProfileUpdateState::Success:
        $profileMessage = 'Profile updated successfully!';
        $successProfile = true;
        break;
}

switch ($viewState->get('password-update-state',PasswordUpdateState::NoUpdate)) {
    case PasswordUpdateState::NoUpdate:
        $showPasswordUpdateBanner = false;
        break;
    case PasswordUpdateState::PasswordInvalid:
        $passwordMessage = 'Password change failed! Password must have at least 8 characters, containing at least 1 number, uppercase letter and lowercase letter.';
        break;
    case PasswordUpdateState::PasswordWrong:
        $passwordMessage = 'Password change failed! Incorrect original password. Please try again.';
        break;
    case PasswordUpdateState::PasswordMismatch:
        $passwordMessage = 'Password change failed! Passwords don\'t match.';
        break;
    case PasswordUpdateState::Success:
        $passwordMessage = 'Password changed successfully! You need to login again on other browsers and devices.';
        $successPassword = true;
        break;
    case PasswordUpdateState::Failure:
        $passwordMessage = 'Password change failed! Critical Server Error! Please try again later.';
        $successPassword = true;
        break;
}


$username = $viewState->get('verified-username');
$isAdmin = $viewState->get('profile-admin','0') === '1';
$accountAge = $viewState->get('profile-age');
$roleName = $viewState->get('profile-role');

$prefillDisplayName = htmlspecialchars($viewState->get('form-display-name'));
$prefillEmail = htmlspecialchars($viewState->get('form-email'));
$prefillSocialWebsite = htmlspecialchars($viewState->get('form-social-website'));

$prefillSocialReddit = htmlspecialchars($viewState->get('form-social-reddit'));
$prefillSocialTwitter = htmlspecialchars($viewState->get('form-social-twitter'));
$prefillSocialInstagram = htmlspecialchars($viewState->get('form-social-instagram'));
$prefillSocialDiscord = htmlspecialchars($viewState->get('form-social-discord'));

$csrfToken = getCsrf('profile');
?>
<main>
    <?php if ($showPictureUpdateBanner): ?>
        <div class="update-banner <?= $successPicture ? CLASS_SUCCESS : CLASS_FAIL?>">
                <?= $pictureMessage ?>
        </div>
    <?php endif; ?>
    <?php if ($showProfileUpdateBanner): ?>
        <div class="update-banner <?= $successProfile ? CLASS_SUCCESS : CLASS_FAIL?>">
                <?= $profileMessage ?>
        </div>
    <?php endif; ?>
    <?php if ($showPasswordUpdateBanner): ?>
        <div class="update-banner <?= $successPassword ? CLASS_SUCCESS : CLASS_FAIL?>">
                <?= $passwordMessage ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <h1>Your User Profile</h1>
        <div class="profile-part">
            <div class="picture-upload-container field">
                <label for="input-profile-picture">Profile Picture:</label>
                <button id="profile-picture-container">
                    <img src="/~dobiapa2/api/internal/users/profile-picture.php?username=<?= $username ?>"
                        alt="Your Profile Picture" id="profile-picture-image">
                </button>
                <input type="file" accept=".jpeg,.jpg,.png,.webp" name="profile-picture" id="input-profile-picture">
                <div id="profile-picture-hint">
                    The image must be JPEG, PNG or WEBP of 8MB at most with aspect ratio 1:1.
                </div>
                <div class="input-row">
                    <label for="input-delete-profile-picture">Delete Picture:</label>
                    <input type="checkbox" name="delete-profile-picture" id="input-delete-profile-picture">
                </div>
            </div>
            <div class="stats-container field">
                <div class="stat-row">
                    <div>
                        <img src="/~dobiapa2/assets/icons/user-circle.svg" class="no-select" alt="User Circle Icon">
                    </div>
                    <div>
                        Your username is <span class="monospace"><?= $username ?></span>.
                    </div>
                </div>
                <div class="stat-row">
                    <div>
                        <img src="/~dobiapa2/assets/icons/clock-arrow.svg" class="no-select" alt="Clock Arrow Icon">
                    </div>
                    <div>
                        Your account is <span class="bold"><?= $accountAge ?></span> old.
                    </div>
                </div>
                <div class="stat-row">
                    <div>
                        <img src="/~dobiapa2/assets/icons/user-group.svg" class="no-select" alt="User Group Icon">
                    </div>
                    <div>
                        Your role is <span class="bold"><?= $roleName ?></span>.
                        <?php if ($isAdmin): ?>
                            <a href="/~dobiapa2/users">You can manage users here.</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-row">
                    <div>
                        <img src="/~dobiapa2/assets/icons/folder-open.svg" class="no-select" alt="Folder Open Icon">
                    </div>
                    <div>
                        <a href="/~dobiapa2/users/<?= $username ?>">View all your projects.</a>
                    </div>
                </div>
                <div class="stat-row">
                    <div>
                        <img src="/~dobiapa2/assets/icons/close.svg" class="no-select" alt="Close Icon">
                    </div>
                    <div>
                        <a href="/~dobiapa2/logout">Log out of your account.</a>
                    </div>
                </div>
            </div>
        </div>
        <p>Tell us more about yourself to enhance your public presence on our website.</p>
        <h2>Basic Details</h2>
        <div class="field">
            <label for="input-display-name">Display Name:</label>
            <input id="input-display-name" name="display-name" placeholder="<?= $username ?>" type="text"
                value="<?= $prefillDisplayName ?>" maxlength="64">
            <label for="input-email">E-mail Address:</label>
            <input id="input-email" name="email" type="text" placeholder="name@domain.tld" value="<?= $prefillEmail ?>" maxlength="200">
            <label for="input-social-website">Personal Website:</label>
            <input id="input-social-website" name="social-website" type="text" placeholder="https://example.com" value="<?= $prefillSocialWebsite ?>" maxlength="150">
        </div>
        <h2>Social Links</h2>
        <div class="field">
            <label for="input-social-reddit">Reddit Username:</label>
            <div class="prefix-container">
                <label for="input-social-reddit">reddit.com/user/</label>
                <input id="input-social-reddit" name="social-reddit" type="text" value="<?= $prefillSocialReddit ?>" maxlength="150">
            </div>
            <label for="input-social-twitter">Twitter Username:</label>
            <div class="prefix-container">
                <label for="input-social-twitter">x.com/</label>
                <input id="input-social-twitter" name="social-twitter" type="text"
                    value="<?= $prefillSocialTwitter ?>" maxlength="150">
            </div>
            <label for="input-social-instagram">Instagram Username:</label>
            <div class="prefix-container">
                <label for="input-social-instagram">instagram.com/</label>
                <input id="input-social-instagram" name="social-instagram" type="text"
                    value="<?= $prefillSocialInstagram ?>" maxlength="150">
            </div>
            <label for="input-social-discord">Discord Server Invite:</label>
            <div class="prefix-container">
                <label for="input-social-discord">discord.gg/</label>
                <input id="input-social-discord" name="social-discord" type="text"
                    value="<?= $prefillSocialDiscord ?>" maxlength="150">
            </div>
        </div>
        <h2>Change Password</h2>
        <div class="field">
            <p>You can change your password here. It needs at least 8 characters, containing at least a single
                number, uppercase letter and lowercase letter. Changing it logs you out of all other sessions!</p>
            <label for="input-password">Current Password:</label>
            <input id="input-password" name="password" type="password" value="">
            <label for="input-password-new">New Password:</label>
            <input id="input-password-new" name="password-new" type="password" value="">
            <label for="input-password-confirm">Confirm New Password:</label>
            <input id="input-password-confirm" name="password-confirm" type="password" value="">
        </div>
        <div>
            <input type="submit" value="Save Changes" disabled>
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
    </form>
</main>