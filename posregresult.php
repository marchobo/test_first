<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'posreg') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「確定する」をクリックしてください。');
}
//ここから

require_once('db/sqlconnect.php');
$pdo = db_connect();
//MYSQLでデータベースにPOSTデータを登録
if($_POST['flag']){
	$st = $pdo -> prepare("UPDATE pdfpos SET posx = ?, posy = ? WHERE id = ?");
	$st->execute(array($_POST['posx'], $_POST['posy'], $_POST['id']));
}
else{
	$st = $pdo -> prepare("INSERT INTO pdfpos VALUES(?, ?, ?, ?)");
	$st->execute(array($_POST['id'], $_POST['posx'], $_POST['posy'], $_POST['pdfid']));
}


//登録後、元の画面に戻る
$_SESSION['click']='posreg';
$_SESSION['pdfid']=$_POST['pdfid'];
header( "Location: posreg.php" ) ;
?>