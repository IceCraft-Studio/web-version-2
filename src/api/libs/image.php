<?php

/**
 * Validates if image at a path belongs into any of specified types.
 * @param string $srcImage The path to the image.
 * @param int[] $validTypes Array of IMAGETYPE_* values.
 * @return bool If the image type belongs into valid types.
 */
function validateImageType($srcImage,$validTypes) {
    $imageType = getimagesize($srcImage)[2];
    foreach ($validTypes as $type) {
        if ($imageType == $type) {
            return true;
        }
    }
    return false;
}


function validateImageAspectRatio($srcImage,$aspectRatio) {
    [$width, $height] = getimagesize($srcImage);
    return (abs($width/$height-$aspectRatio) < 0.01);
}

function saveImageAsWebP($srcImage,$outImage,$outWidth = 0,$outHeight = 0) {
    // Get basic details
    [$width, $height, $type] = getimagesize($srcImage);
    if ($outWidth == 0) {
        $outWidth = $width;
    }
    if ($outHeight == 0) {
        $outHeight = $height;
    }
    // Load the image properly
    $imageData = match ($type) {
        IMAGETYPE_PNG  => imagecreatefrompng($srcImage),
        IMAGETYPE_JPEG => imagecreatefromjpeg($srcImage),
        IMAGETYPE_WEBP => imagecreatefromwebp($srcImage),
        default => false
    };
    if ($imageData == false) {
        return false;
    }
    imagealphablending($imageData, false);
    imagesavealpha($imageData, true);
    // Resize if needed
    if ($width != $outWidth || $height != $outHeight) {
        imagecopyresampled($imageData,$imageData,0,0,0,0,$outWidth,$outHeight,$width,$height);
    }
    // Save as WEBP
    imagewebp($imageData,$outImage);
}