<main>
    <h1>Register</h1>
    <form method="POST" action="">
        <div class="row">
            <label for="register_username">Username:</label>
            <input type="text" maxlength="32" name="register_username">
            <div class="indicator" data-for="register_username"></div>
        </div>
        <div class="row">
            <label for="register_password">Password:</label>
            <input type="password" maxlength="128" name="register_password">
            <div class="indicator" data-for="register_password"></div>
        </div>
        <div class="row">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" maxlength="128" name="confirm_password">
            <div class="indicator" data-for="confirm_password"></div>
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