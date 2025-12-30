<?php
$viewState = ViewData::getInstance();

$username = $viewState->get('username');

$csrfToken = $_SESSION['csrf-token'];
?>
<main>
    <h1>Your User Profile</h1>
    <div>
        Your username is <span class="monospace"><?= $username ?></span>.
        Your account is <span class="bold"><?= $accountAge ?></span> old.
    </div>
    <a>
        View all your projects.
    </a href="/~dobiapa2/users/<?= $username ?>">
    <form enctype="multi">
        <h2>Basic Details</h2>
        <div class="field">
            <label for="input-display-name" id="slug-prefix">/projects/</label>
            <input id="input-slug" name="slug" type="text" value="">
        </div>
        <h2>Social Links</h2>
        <div>

        </div>
        <h2>Change Password</h2>
        <div>

        </div>
        <input type="hidden" name="csrf-token" value="<?= $csrfToken ?>">
        <input type="submit" value="Edit Profile">

    </form>
</main>