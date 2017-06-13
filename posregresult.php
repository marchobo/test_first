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
//連想配列を受取る
$flags = $_POST['flag'];
$posxs = $_POST['posx'];
$posys = $_POST['posy'];
$ids = $_POST['id'];

require_once('db/sqlconnect.php');
$pdo = db_connect();
//MYSQLでデータベースにPOSTデータを登録
foreach($ids as $key=>$id){
	if($flags[$key]){
		$st = $pdo -> prepare("UPDATE pdfpos SET posx = ?, posy = ? WHERE id = ?");
		$st->execute(array($posxs[$key], $posys[$key], $id));
	}
	else{
		$st = $pdo -> prepare("INSERT INTO pdfpos VALUES(?, ?, ?, ?)");
		$st->execute(array($id, $posxs[$key], $posys[$key], $_POST['pdfid']));
	}
}

//登録後、元の画面に戻る
$_SESSION['click']='posreg';
$_SESSION['pdfid']=$_POST['pdfid'];
header( "Location: posreg.php" ) ;
?>