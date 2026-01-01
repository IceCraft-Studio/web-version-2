<?php
$viewState = ViewData::getInstance();
$prefillUsername = htmlspecialchars($viewState->get('form-username'));
$showError = true;
$errorMessage = '';

switch ($viewState->get('register-error', RegisterFormError::None)) {
    case RegisterFormError::None:
        $showError = false;
        break;
    case RegisterFormError::CsrfInvalid:
        $errorMessage = 'Critical client error! Please try resending the form.';
        break;
    case RegisterFormError::UsernameInvalid:
        $errorMessage = 'Username must be between 4 and 48 characters long! It can only contain lowercase letters, numbers and singular hyphens between words!';
        break;
    case RegisterFormError::UsernameTaken:
        $errorMessage = 'Selected username is already taken. Please try another one.';
        break;
    case RegisterFormError::PasswordInvalid:
        $errorMessage = 'The password must be between 8 and 128 (inclusive) characters long!';
        break;
    case RegisterFormError::PasswordMismatch:
        $errorMessage = 'Passwords don\'t match!';
        break;
    case RegisterFormError::ServerDatabase:
        $errorMessage = 'Critical server error! Please try again later.';
        break;
}
$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <h1>Register</h1>
    <form method="POST" action="">
        <div class="row">
            <label for="username">Username:</label>
            <div class="input-group">
                <input type="text" maxlength="32" name="username" value="<?= $prefillUsername ?>" required>
                <div class="indicator" data-for="username"></div>
            </div>
        </div>
        <div class="row">
            <label for="password">Password:</label>
            <div class="input-group">
                <input type="password" maxlength="128" name="password" required>
                <div class="indicator" data-for="password"></div>
            </div>
        </div>
        <div class="row">
            <label for="confirm-password">Confirm Password:</label>
            <div class="input-group">
                <input type="password" maxlength="128" name="confirm-password" required>
                <div class="indicator" data-for="confirm-password"></div>
            </div>
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
        <div class="row">
            <input type="submit" value="Register" disabled>
        </div>
        <?php if ($showError): ?>
            <div class="row bold color-required">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>
    </form>
    <div>
        Already have an IceCraft Projects account? <a href="/~dobiapa2/login">Login right here!</a>
    </div>
</main>