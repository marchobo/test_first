<?php

function db_connect(){
	$dsn = 'mysql:host=localhost;dbname=db_test_1';
	$user = 'root';
	$password = 't873n338';

	try{
		$dbh = new PDO($dsn, $user, $password);
		return $dbh;
	}catch (PDOException $e){
	    	print('Error:'.$e->getMessage());
	    	die();
	}
}