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
    <a href="#page-top">Return to top.</a>
    </div>
</main>