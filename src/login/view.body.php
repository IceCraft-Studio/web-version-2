<?php
$prefillUsername = isset($_POST['login_username']) ? htmlspecialchars($_POST['login_username']) : '';
$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <h1>Login</h1>
    <form action="" method="POST">
        <div class="row">
            <label for="login_username">Username:</label>
            <input type="text" minlength="4" maxlength="32" name="login_username" value="<?= $prefillUsername ?>">
        </div>
        <div class="row">
            <label for="login_password">Password:</label>
            <input type="password" minlength="8" maxlength="128" name="login_password">
        </div>
        <div class="row">
            <input type="submit">
        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
    </form>
    <div>
        New to IceCraft Projects? <a href="/~dobiapa2/register">Register a new account!</a>
    </div>
</main>