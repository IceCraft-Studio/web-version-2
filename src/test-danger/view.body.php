<?php
$dbConnect = DbConnect::getConnection(getDbAccessObject());
var_dump(dbQuery($dbConnect,'SELECT * FROM `role` WHERE `id` = ?',"s",["admin"]));
phpinfo();
?>