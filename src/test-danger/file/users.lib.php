<?php

const DB_FILE_NAME = 'users.json';

$file = file_get_contents(DB_FILE_NAME);
// handle errors bitch

$db = json_decode($file,true);



function list_users() {
    global $db;
    return $db;
}

function get_user($id) {
    global $db;
    foreach ($db as $user) {
        if ($id == $user['id']) {
            return $user;
        }
    }
    return null;
}

function add_user($name, $email, $avatar) {
}

function delete_user($id) {
}

function edit_user($id, $name, $email, $avatar) {
}

?>
