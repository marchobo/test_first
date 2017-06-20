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
$itemflags = $_POST['itemflag'];
$itemids = $_POST['itemid'];
$itemposxs = $_POST['itemposx'];
$itemposys = $_POST['itemposy'];
$id_subs = $_POST['id_sub'];
$imgflags = $_POST['imgflag'];
$imgids = $_POST['imgid'];
$imgposxs = $_POST['imgposx'];
$imgposys = $_POST['imgposy'];
$id_imgitems = $_POST['id_imgitem'];

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

//MYSQLでデータベースにPOSTデータを登録(項目)
foreach($id_subs as $key=>$id){
	if($itemflags[$key]){
		$st = $pdo -> prepare("UPDATE pdfpos_sub SET posx = ?, posy = ? WHERE id = ?");
		$st->execute(array($itemposxs[$key], $itemposys[$key], $id));
	}
	else{
		$st = $pdo -> prepare("INSERT INTO pdfpos_sub VALUES(?, ?, ?, ?, ?)");
		$st->execute(array(0, $itemids[$key], $_POST['pdfid'], $itemposxs[$key], $itemposys[$key]));
	}

}

//MYSQLでデータベースにPOSTデータを登録(画像)
foreach($id_imgitems as $key=>$id){
	if($imgflags[$key]){
		$st = $pdo -> prepare("UPDATE pdfpos_img SET posx = ?, posy = ? WHERE id = ?");
		$st->execute(array($imgposxs[$key], $imgposys[$key], $id));
	}
	else{
		$st = $pdo -> prepare("INSERT INTO pdfpos_img VALUES(?, ?, ?, ?, ?)");
		$st->execute(array(0, $imgids[$key], $_POST['pdfid'], $imgposxs[$key], $imgposys[$key]));
	}

}

//登録後、元の画面に戻る
$_SESSION['click']='posreg';
$_SESSION['pdfid']=$_POST['pdfid'];
header( "Location: posreg.php" ) ;
?>