<?php
const DB_FILE_NAME = '/home/dobiapa2/data/data.json';
$file = file_get_contents(DB_FILE_NAME);
$db = json_decode($file, true);
//var_dump($db);

function get_user($username)
{
    global $db;
    foreach ($db['users'] as $user) {
        if ($user['user'] == $username) {
            return $user;
        }
    }

}

function get_notes($user_id)
{
    global $db;
    $notes = [];
    foreach ($db['notes'] as $note) {
        if ($note['userId'] == $user_id) {
            $notes[] = $note;
        }
    }
    return $notes;
}