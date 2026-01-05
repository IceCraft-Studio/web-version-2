<?php

$viewState = ViewData::getInstance();
$usersList = $viewState->get('users-list');

function generateUserTableData($usersList) {
    foreach ($usersList as $user) {
        $user['username']
        echo '<td><img src="' . $user['username']  . '" alt="' . $user['username'] . '"></td>';
        echo '<td>' .  . '</td>'
        echo '<td>' .  . '</td>'
        echo '<td>' .  . '</td>'
    }
}

?>
<main>
    <table>
        <tr>
            <th>Picture</th>
            <th>Username</th>
            <th>Display Name</th>
            <th>Datetime Created</th>
        </tr>
        <?= generateUserTableData($usersList) ?>
    </table>
    <?= var_dump($viewState->get('users-list')) ?>
</main>