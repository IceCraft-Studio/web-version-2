<?php

/**
 * Given a file name in a certain directory, returns path with the same name if available, or tries appending numbers to the name until it finds available one.
 * @param mixed $directory Directory to test the file names in.
 * @param mixed $name The file name without the extension.
 * @param mixed $extension File extension, when emtpy assumes a file without extension.
 * @return string Returns the full path where it is possible to create a new file.
 */
function getAvailablepath($directory,$name,$extension = '') {
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
 * @param mixed $directory Directory to remove.
 * @return void
 */
function removeDirRecursive($directory) {
    foreach (scandir($directory) as $file) {
        if ($file === '.' || $file === '..') continue;
        $fullPath = $directory . '/' . $file;
        if (is_dir($fullPath)) {
            removeDirRecursive($fullPath);
        }
        unlink($fullPath);
    }
    rmdir($directory);
}