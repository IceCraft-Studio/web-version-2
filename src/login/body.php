<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['login_username'] == "user" && $_POST['login_password'] == "password") {
        
    } else {

    }
}
?>
<main>
    <h1>Login</h1>
    <form action="" method="POST">
        <div class="row">
            <label for="login_username">Username:</label>
            <input type="text" minlength="4" maxlength="32" name="login_username">
        </div>
        <div class="row">
            <label for="login_password">Password:</label>
            <input type="password" minlength="8" maxlength="128" name="login_password">
        </div>
        <div class="row">
            <input type="submit">
        </div>
    </form>
    <div>
        New to IceCraft Projects? <a href="/~dobiapa2/register">Register a new account!</a>
    </div>
</main>