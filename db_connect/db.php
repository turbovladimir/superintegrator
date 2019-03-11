<?php
include_once 'safemysql.class.php';
include_once 'connect_vars.php';

$db = new SafeMySQL(array("host" => "$host", "user" => "$dbuser", "pass" => "$dbpass", "db" => "$dbname", "charset" => "utf8"));
?>