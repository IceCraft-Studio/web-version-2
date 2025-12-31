<?php
$viewState = ViewData::getInstance();
$prefillUsername = htmlspecialchars($viewState->get('form-username'));
?>
<main>
    <h1>Register</h1>
    <form method="POST" action="">
        <div class="row">
            <label for="username">Username:</label>
            <input type="text" maxlength="32" name="username" value="<?= $prefillUsername ?>" required>
            <div class="indicator" data-for="username"></div>
        </div>
        <div class="row">
            <label for="password">Password:</label>
            <input type="password" maxlength="128" name="password" required>
            <div class="indicator" data-for="password"></div>
        </div>
        <div class="row">
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" maxlength="128" name="confirm-password" required>
            <div class="indicator" data-for="confirm-password"></div>
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
        <div class="row">
            <input type="submit" value="Register" disabled>
        </div>
    </form>
    <div>
        Already have an IceCraft Projects account? <a href="/~dobiapa2/login">Login right here!</a>
    </div>
</main>