<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'shikendata') {
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
$flags = $_POST['flag'];
$mantens = $_POST['manten'];
$kaitous = $_POST['kaitou'];

//大問番号順に登録、pdfidとdaimonで一意に決定
for($i=0;$i<$_POST['daimonsu'];$i++){
	if($flags[$i]){
		$st = $pdo -> prepare("UPDATE shikendata SET manten = ?, kaitou = ? WHERE pdfid = ? and daimon = ?");
		$st->execute(array($mantens[$i], $kaitous[$i], $_POST['pdfid'], $i+1));
	}
	else{
		$st = $pdo -> prepare("INSERT INTO shikendata VALUES(?, ?, ?, ?, ?)");
		$st->execute(array(0, $_POST['pdfid'], $i+1, $mantens[$i], $kaitous[$i]));
	}
}
//大問番号が大問数より大きいものは削除
$st = $pdo -> prepare("DELETE FROM shikendata where pdfid = ? and daimon > ?");
$st->execute(array($_POST['pdfid'], $_POST['daimonsu']));

//登録後、元の画面に戻る
$_SESSION['click']='shikendata';
$_SESSION['pdfid']=$_POST['pdfid'];
header( "Location: shikendata.php" );
?>