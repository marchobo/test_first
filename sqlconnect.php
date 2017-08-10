<?php
function db_connect(){
	//DB接続
	try{
		$server = "mysql:host=localhost;dbname=userdb";
		$username = "root";
		$password = "nagase12345678";
		$pdo = new PDO($server, $username, $password);
	}
	catch (PDOException $e) {
		print "エラー!: " . $e->getMessage() . "<br/>";
		die();
	}
	return $pdo;
}
?>