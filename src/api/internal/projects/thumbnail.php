<?php
require "../../libs/helpers.php";

$project = $_GET['project'];
$category = $_GET['category'];

$image_path = resolveDataPath('project/' . $category . '/' . $project . '/thumbnail.webp');

if (file_exists($image_path)) {
    header('Content-Type: image/webp',true);
    readfile($image_path);
} else {
    header('Location: /~dobiapa2/assets/empty-thumbnail.webp', true, 302);
}