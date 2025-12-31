<?php
$dbConnect = DbConnect::getConnection(getDbAccessObject());
var_dump(dbQuery($dbConnect,'SHOW TABLES'));
phpinfo();
?>