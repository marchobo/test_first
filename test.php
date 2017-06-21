<?php
require_once('db/sqlconnect.php');
$pdo = db_connect();
$pdo->query("set time_zone = '+09:00'");
$st = $pdo->query("SELECT now()");
foreach($st as $key => $value){
	$now = $value[0];
}
var_dump($now);
?>