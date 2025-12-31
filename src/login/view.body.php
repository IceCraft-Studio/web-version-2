<?php
$viewState = ViewData::getInstance();
$loginError = $viewState->get('login-error');
$csrfError = $viewState->get('csrf-error');
$prefillUsername = htmlspecialchars($viewState->get('login-form-username'));
$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <h1>Login</h1>
    <form action="" method="POST">
        <div class="row">
            <label for="username">Username:</label>
            <input type="text" maxlength="32" name="username" value="<?= $prefillUsername ?>" required>
        </div>
        <div class="row">
            <label for="password">Password:</label>
            <input type="password" maxlength="128" name="password" required>
        </div>
        <div class="row">
            <input type="submit" value="Login" disabled>
        </div>
        <?php if ($loginError === 1): ?>
        <div class="row bold color-required">
            Wrong username or password! Please try again.
        </div>
        <?php endif; ?>
        <?php if ($csrfError === 1): ?>
        <div class="row bold color-required">
            Critical client error! Please try resending the form.
        </div>
        <?php endif; ?>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
    </form>
    <div>
        New to IceCraft Projects? <a href="/~dobiapa2/register">Register a new account!</a>
    </div>
</main>