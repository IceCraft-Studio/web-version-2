<?php
$viewState = ViewData::getInstance();
$showError = true;
switch ($viewState->get('login-error',LoginFormError::None)) {
    case LoginFormError::None:
        $showError = false;
        break;
    case LoginFormError::CsrfInvalid:
        $errorMessage = 'Critical client error! Please try resending the form.';
        break;
    case LoginFormError::WrongCredentials:
        $errorMessage = 'Wrong username or password! Please try again.';
        break;
}
$prefillUsername = htmlspecialchars($viewState->get('form-username'));
$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <h1>Login</h1>
    <form action="" method="POST">
        <div class="row">
            <label for="input-username">Username:</label>
            <input type="text" id="input-username" name="username" value="<?= $prefillUsername ?>" required>
        </div>
        <div class="row">
            <label for="input-password">Password:</label>
            <input type="password" id="input-password" name="password" required>
        </div>
        <?php if ($showError): ?>
        <div class="row bold color-required center-text">
            <?= $errorMessage ?>
        </div>
        <?php endif; ?>
        <div class="row">
            <input type="submit" value="Login">
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
    </form>
    <div class="center-text">
        New to IceCraft Projects? <a href="/~dobiapa2/register">Register a new account!</a>
    </div>
</main>