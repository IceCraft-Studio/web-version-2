<?php
#echo file_get_contents("test.html");

function hezkyCesky($dayNumber) {
    $vpohoArray = array("Pondeli","Utery","Streda","Ctvrtek","Patek","Sobota","Nedele");
    $retardArray = array(1=>"Pondeli","Utery","Streda","Ctvrtek","Patek","Sobota","Nedele");
    return $vpohoArray[$dayNumber-1];
}

$date = date('d.m.Y.');
$exploded = explode('.',$date);
$day = $exploded[0];
$month = $exploded[1];
$year = $exploded[2];
$timestamp = mktime(0,0,0,$month,$day,$year);
echo $date . " je " . hezkyCesky(date('N')) . "<br>";
echo $timestamp;
?>