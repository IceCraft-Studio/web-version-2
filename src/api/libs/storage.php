<?php

/**
 * Given a file name in a certain directory, returns path with the same name if available, or tries appending numbers to the name until it finds available one.
 * @param string $directory Directory to test the file names in.
 * @param string $name The file name without the extension.
 * @param string $extension File extension, when empty assumes a file without extension.
 * @return string Returns the full path where it is possible to create a new file.
 */
function getAvailablePath($directory,$name,$extension = '') {
    if (!str_ends_with($directory,'/')) {
        $directory = $directory . '/';
    }
    $finalPath = $directory . $name;
    if ($extension != '') {
        $finalPath = $finalPath . '.' . $extension;
    }
    $number = 1;
    while (file_exists($finalPath)) {
        $finalPath = $directory . $name . '-' . (string)$number;
        if ($extension != '') {
            $finalPath = $finalPath . '.' . $extension;
        }
        $number++;
    }
    return $finalPath;
}

/**
 * Recursively removes an entire directory tree stemming from the specified directory.
 * @param string $directory Directory to remove.
 * @return void
 */
function removeDirRecursive($directory) {
    foreach (scandir($directory) as $file) {
        if ($file == '.' || $file == '..') continue;
        $fullPath = $directory . '/' . $file;
        if (is_dir($fullPath)) {
            removeDirRecursive($fullPath);
        }
        unlink($fullPath);
    }
    rmdir($directory);
}

/**
 * Creates a safe file name by keeping only safe characters like letters, numbers hyphens and dots.
 * @param string $fileName The original text string to be converted into a file name.
 * @return string
 */
function createSafeFileName($fileName) {
    $fileName = strtolower($fileName);
    $fileName = preg_replace('/[\s\b_]/', '-', $fileName);
    $fileName = preg_replace('/[^A-z0-9-\.]/', '', $fileName);
    if (strlen($fileName) > 120) {
        $fileName = substr($fileName,0,120);
    }
    return $fileName;
}