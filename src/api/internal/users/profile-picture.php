<?php
$username = $_GET['username'];
$image_path = "/home/dobiapa2/data/profile-pictures/$username.webp";

if (file_exists($image_path)) {
    header('Content-Type: image/webp',true);
    readfile($image_path);
} else {
    header('Location: /~dobiapa2/assets/icons/default-steve.webp', true, 302);
}
