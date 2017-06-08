<?php
//データ削除用
require_once('sqlconnect.php');
$pdo = db_connect();
//MYSQLでデータベースからPOSTデータを削除
$st = $pdo -> prepare("DELETE FROM koumoku WHERE id = ?");
$st->execute(array($_POST['id']));

//登録後、元の画面に戻る
header( "Location: regist.php" ) ;
?>