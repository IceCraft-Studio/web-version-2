<?php

const DB_FILE_NAME = 'users.json';

$file = file_get_contents(DB_FILE_NAME);
// handle errors bitch

$db = json_decode($file,true);


function save() {
    global $db;
    $json = json_encode($db);
    file_put_contents(DB_FILE_NAME, $json);
}
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
    global $db;
    $id = uniqid();
    $user = array(
        'id'=>$id,
        'name'=> $name,
        'email'=> $email,
        'avatar'=> $avatar
        );
    $db[] = $user;
    save();
}

function delete_user($id) {
    global $db;
    foreach ($db as $user) {
        if ($id == $user['id']) {
            unset($user);
        }
    }
    save();
}

function edit_user($id, $name, $email, $avatar) {
    global $db;
    $user = array(
        'id'=>$id,
        'name'=> $name,
        'email'=> $email,
        'avatar'=> $avatar
        );
    $db[$id] = $user;
    save();
}

?>
