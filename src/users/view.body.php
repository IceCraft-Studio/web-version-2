<?php

$viewState = ViewData::getInstance();
$usersList = $viewState->get('users-list');


function generateUserTableData($usersList) {
    foreach ($usersList as $user) {
        $userUsername = $user['username'] ?? '';
        $dateCreated = date('d/m/Y G:i:s',strtotime($user['datetime_created']));
        echo '<tr>';
        echo '<td><img src="' . getUserPictureLink($userUsername)  . '" alt="' . $userUsername . '\'s Profile Picture"></td>';
        echo '<td><a href="' . getUserLink($userUsername) . '">' . $userUsername . '</a></td>';
        echo '<td>' . $user['display_name'] ?? '' . '</td>';
        echo '<td>' . $user['role'] ?? '' . '</td>';
        echo '<td>' . $dateCreated . '</td>';
        echo '</tr>';
    }
}

?>
<main>
    <h1 id="page-top">Manage Users</h1>
    <p>Here you are provided with a list of all users, openning the link on their username leads to their user page where you can manage their accounts.</p>
    <div id="page-controls">
        <div class="page-links">

        </div>
        <div class="page-form">
            <form method="get">
                <label for="select-page-size">Users per page:</label>
                <select id="select-page-size" name="size">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                </select>
                <label for="select-page-sort">Filter Role:</label>
                <select id="select-page-sort" name="role">
                    <option value="ban">Banned</option>
                    <option value="user">User</option>
                    <option value="admin">Administrator</option>
                    <option value="owner">Website Owner</option>
                </select>
                <label for="select-page-sort">Sort by:</label>
                <select id="select-page-sort" name="sort">
                    <option value="username">Username</option>
                    <option value="display_name">Display Name</option>
                    <option value="modified">Modified</option>
                </select>
                <label for="select-page-order">Sort order:</label>
                <select id="select-page-order" name="order">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
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