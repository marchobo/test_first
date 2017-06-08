<?php
function db_connect(){
	//DB接続
	try{
		$server = "mysql:host=localhost;dbname=hukushudb";
		$username = "root";
		$password = "t873n338";
		$pdo = new PDO($server, $username, $password);
	}
	catch (PDOException $e) {
		print "エラー!: " . $e->getMessage() . "<br/>";
		die();
	}
	return $pdo;
}
?>