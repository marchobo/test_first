<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'exdelete') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「削除する」をクリックしてください。');
}
//データ削除用
require_once('db/userdb.php');
$pdo = db_connect();
//MYSQLでデータベースからPOSTデータを削除
$st = $pdo -> prepare("DELETE FROM examiner WHERE id = ?");
$st->execute(array($_POST['id']));

//登録後、元の画面に戻る
header( "Location: exregist.php") ;

?>