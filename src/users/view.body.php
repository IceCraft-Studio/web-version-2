<?php

$viewState = ViewData::getInstance();
$usersList = $viewState->get('users-list');


function generateUserTableData($usersList)
{
    foreach ($usersList as $user) {
        $userUsername = $user['username'] ?? '';
        $dateCreated = date('d/m/Y G:i:s', strtotime($user['datetime_created']));
        echo '<tr>';
        echo '<td><img src="' . getUserPictureLink($userUsername) . '" alt="' . $userUsername . '\'s Profile Picture"></td>';
        echo '<td><a href="' . getUserLink($userUsername) . '">' . $userUsername . '</a></td>';
        echo '<td>' . $user['display_name'] ?? '' . '</td>';
        echo '<td>' . $user['role'] ?? '' . '</td>';
        echo '<td>' . $dateCreated . '</td>';
        echo '</tr>';
    }
}

$defaultSizes = ['10','25','50','100','200'];

$currentSize = $_GET['size'] ?? '25';
if (!in_array($currentSize,$defaultSizes)) {
    $defaultSizes[] = $currentSize;
}
$currentRole = $_GET['role'] ?? '';
$currentSort = $_GET['sort'] ?? 'username';
$currentOrder = $_GET['order'] ?? 'asc';


?>
<main>
    <h1 id="page-top">Manage Users</h1>
    <p>Here you are provided with a list of all users, openning the link on their username leads to their user page
        where you can manage their accounts.</p>
    <div id="page-controls">
        <div class="page-links">

        </div>
        <div class="page-form">
            <form method="get">
                <div>
                    <label for="select-page-size">Users per page:</label>
                    <select id="select-page-size" name="size">
                        <?php
                        foreach ($defaultSizes as $someSize) {
                            echo '<option value="' . $someSize . '" ' . $someSize == $currentSize ? 'selected' : '' . '>'. $someSize . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="select-page-sort">Filter Role:</label>
                    <select id="select-page-sort" name="role">
                        <option value="" <?= $currentRole === '' ? 'selected' : '' ?>>All Roles</option>
                        <option value="ban" <?= $currentRole === 'ban' ? 'selected' : '' ?>>Banned</option>
                        <option value="user" <?= $currentRole === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        <option value="owner" <?= $currentRole === 'owner' ? 'selected' : '' ?>>Website Owner</option>
                    </select>
                </div>
                <div>
                    <label for="select-page-sort">Sort by:</label>
                    <select id="select-page-sort" name="sort">
                        <option value="username" <?= $currentSort === 'username' ? 'selected' : '' ?>>Username</option>
                        <option value="display_name" <?= $currentSort === 'display_name' ? 'selected' : '' ?>>Display Name
                        </option>
                        <option value="modified" <?= $currentSort === 'modified' ? 'selected' : '' ?>>Modified</option>
                    </select>
                </div>
                <div>
                    <label for="select-page-order">Sort order:</label>
                    <select id="select-page-order" name="order">
                        <option value="asc" <?= $currentOrder === 'asc' ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= $currentOrder === 'desc' ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
                <input type="submit" value="Apply">
            </form>
        </div>
    </div>
    <div class="horizontal-scroll">
        <table>
            <tr>
                <th>Picture</th>
                <th>Username</th>
                <th>Display Name</th>
                <th>Role</th>
                <th>Datetime Created</th>
            </tr>
            <?php
            generateUserTableData($usersList)
                ?>
        </table>
    </div>
    <a href="#page-top">Return to top.</a>
</main>