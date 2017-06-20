<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'pdfdelete') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「削除する」をクリックしてください。');
}
//データ削除用(pdf, 項目, pdfposの順)
require_once('db/sqlconnect.php');
$pdo = db_connect();
//MYSQLでデータベースからPOSTデータを削除
$st = $pdo -> prepare("DELETE FROM pdf WHERE id = ?");
$st->execute(array($_POST['id']));
$st = $pdo -> prepare("DELETE FROM koumoku WHERE pdfid = ?");
$st->execute(array($_POST['id']));
$st = $pdo -> prepare("DELETE FROM pdfpos WHERE pdfid = ?");
$st->execute(array($_POST['id']));
$st = $pdo -> prepare("DELETE FROM shikendata WHERE pdfid = ?");
$st->execute(array($_POST['id']));

//登録後、元の画面に戻る
header( "Location: upload.php") ;

?>