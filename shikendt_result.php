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


//登録後、元の画面に戻る
$_SESSION['click']='shikendata';
$_SESSION['pdfid']=$_POST['pdfid'];
header( "Location: shikendata.php" );
?>