<link rel="stylesheet" type="text/css" href="css/menu.css">
<?php
include('menu.html');
?>
<br>
<?php
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'upload') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「アップロード」をクリックしてください。');
}
//データ更新用
require_once('session_check.php');
require_once('sqlconnect.php');

// ファイルの保存先
$uploadfile = 'templates/'.$_POST['univcode'].$_POST['shikenshu'].$_POST['nendo'].'.pdf';
//エラーコード2だった場合（HTMLのファイル制限超過）
if ($_FILES['upload']['error'] === 2) {
	die('ファイルサイズを小さくしてください！');

//サイズが0だった場合（ファイルが空）
} elseif ($_FILES['upload']['size'] === 0) {
	die('ファイルを選択してください！');

//PDFファイルじゃなかった場合
} elseif ($_FILES['upload']['type'] !== 'application/pdf') {
	die('PDFファイルを選択してください！');
}
// アップロードされたファイルに、パスとファイル名を設定して保存
move_uploaded_file($_FILES['upload']['tmp_name'], $uploadfile);

//SQLで保存
$pdo = db_connect();
//SQLで検索し、既存のデータではないか確認する
$sql = "select * from pdf where univcode = ? and shikenshu = ? and nendo = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($_POST['univcode'],$_POST['shikenshu'], $_POST['nendo']));
$count = $st->rowCount();
if ($count != 0){
	$pdo = null;
	die('登録済みのデータです。');
}
//MYSQLでデータベースにPOSTデータを登録
$st = $pdo -> prepare("INSERT INTO pdf VALUES(?, ?, ?, ?)");
$st->execute(array(0, $_POST['univcode'],$_POST['shikenshu'], $_POST['nendo']));


//登録後、元の画面に戻る
header( "Location: upload.php" ) ;
?>