<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'regist') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「項目を登録」をクリックしてください。');
}
//ここから
require_once('sqlconnect.php');
//SQLで検索し、既存のデータではないか確認する
$pdo = db_connect();
$sql = "select * from koumoku where pdfid = ? and daimon = ? and shomon = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($_POST['pdfid'],$_POST['daimon'], $_POST['shomon']));
$count = $st->rowCount();
if ($count != 0){
	$_SESSION['click']='regist';
	die('登録済みのデータです。');
}
//MYSQLでデータベースにPOSTデータを登録
$st = $pdo -> prepare("INSERT INTO koumoku VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$st->execute(array(0, $_POST['univcode'], $_POST['nendo'], $_POST['shikenshu'], $_POST['daimon'],$_POST['shomon'], $_POST['mathcode'],$_POST['haiten'], $_POST['rank'], $_POST['pdfid']));



//登録後、元の画面に戻る
$_SESSION['click']='regist';
header( "Location: regist.php" ) ;
?>
