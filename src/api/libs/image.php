<?php

/**
 * Validates if image at a path belongs into any of specified types.
 * @param string $srcImage The path to the image.
 * @param int[] $validTypes Array of IMAGETYPE_* values.
 * @return bool Result of the validation.
 */
function validateImageType($srcImage,$validTypes) {
    $imageInfo = getimagesize($srcImage);
    if ($imageInfo === false) {
        return false;
    }
    $imageType = $imageInfo[2];
    foreach ($validTypes as $type) {
        if ($imageType == $type) {
            return true;
        }
    }
    return false;
}

/**
 * Validates that the given image's dimensions match the given aspect ratio.
 * @param mixed $srcImage The path to the source image.
 * @param mixed $aspectRatio The target aspect ratio that needs to match.
 * @param float $precision Optionally set the magin of error for precision - 0 means exact match is required to return `true`.
 * @return bool Result of the validation.
 */
function validateImageAspectRatio($srcImage,$aspectRatio,$precision = 0.01) {
    $imageInfo = getimagesize($srcImage);
    if ($imageInfo === false) {
        return false;
    }
    [$width, $height] = $imageInfo;
    return (abs($width/$height-$aspectRatio) < $precision);
}

/**
 * Saves the specified image as a WEBP at a specified location. (Optionally resampled to specified dimensions.) Supports PNG, JPEG and WEBP. GIF is also 
 * @param mixed $srcImage Path to the image to save.
 * @param mixed $outImage Path where to save the image. WITHOUT THE EXTENSION!
 * @param int $outWidth Output width, use `0` means don't change.
 * @param int $outHeight Output height, `0` means dont't change.
 * @return string|bool Output path with extension on success, `false` on failure.
 */
function saveImageAsWebpOrGif($srcImage,$outImage,$outWidth = 0,$outHeight = 0) {
    // Get basic details
    $imageInfo = getimagesize($srcImage);
    if ($imageInfo === false) {
        return false;
    }
    [$width, $height, $type]  = $imageInfo;
    if ($outWidth === 0 || $outWidth > $width) {
        $outWidth = $width;
    }
    if ($outHeight === 0 || $outHeight > $height) {
        $outHeight = $height;
    }
    // Load the image properly
    $imageData = match ($type) {
        IMAGETYPE_PNG  => imagecreatefrompng($srcImage),
        IMAGETYPE_JPEG => imagecreatefromjpeg($srcImage),
        IMAGETYPE_WEBP => imagecreatefromwebp($srcImage),
        IMAGETYPE_GIF => imagecreatefromgif($srcImage),
        default => false
    };
    if ($imageData == false) {
        return false;
    }
    // Resample
    $newImageData = imagecreatetruecolor($outWidth,$outHeight);
    imagealphablending($newImageData, false);
    imagesavealpha($newImageData, true);
    imagecopyresampled($newImageData,$imageData,0,0,0,0,$outWidth,$outHeight,$width,$height);
    // Save as WEBP or GIF and return the new path or `false`
    if ($type === IMAGETYPE_GIF && imagegif($newImageData,$outImage . '.gif')) {
        return $outImage . '.gif';
    } else if (imagewebp($newImageData,$outImage . '.webp')) {
        return $outImage . '.webp';
    }
    return false;

}