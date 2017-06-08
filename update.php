<?php
require_once('sqlconnect.php');
$pdo = db_connect();
//MYSQLでデータベース上のデータをアップデート
$st = $pdo -> prepare("INSERT INTO koumoku VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
$st->execute(array(0, 1000, 16, 2, $_POST['daimon'],$_POST['shomon'], $_POST['mathcode'],$_POST['haiten'], $_POST['rank']));

//登録後、元の画面に戻る
header( "Location: regist.php" ) ;
?>