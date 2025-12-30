<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

$username = $_GET['username'];

$image_path = resolveDataPath('user/' . $username . '/profile-picture.webp');

if (file_exists($image_path)) {
    header('Content-Type: image/webp',true);
    readfile($image_path);
} else {
    header('Location: /~dobiapa2/assets/icons/default-steve.webp', true, 302);
}
