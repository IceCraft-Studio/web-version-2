<?php
$page = $_GET['page'] ?? 1;
$page = is_numeric($page) ? $page : 1;

$size = $_GET['size'] ?? 1;
$size = is_numeric($size) ? $size : 1;
$size = max(1, min($size, 5));
$sizeTable = [1 => 10,25,50,100,200];
$trueSize = $sizeTable[$size]




?>
<main>
    
</main>