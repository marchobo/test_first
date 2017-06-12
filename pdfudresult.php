<?php
//データ更新用
require_once('session_check.php');
require_once('db/sqlconnect.php');

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

//ここからSQL
$pdo = db_connect();
// UPDATE文を変数に格納(pdf,項目の順)
$sql = "UPDATE pdf SET univcode = ?, shikenshu = ?, nendo = ? ,daimonsu = ? WHERE id = ?";
// 更新する値と該当のIDは空のまま、SQL実行の準備をする
$stmt = $pdo->prepare($sql);
// 更新する値と該当のIDを配列に格納する
$params = array($_POST['univcode'],$_POST['shikenshu'], $_POST['nendo'], $_POST['daimonsu'], $_POST['id']);
// 更新する値と該当のIDが入った変数をexecuteにセットしてSQLを実行
$stmt->execute($params);

//項目
$sql = "UPDATE koumoku SET univcode = ?, shikenshu = ?, nendo = ? WHERE pdfid = ?";
// 更新する値と該当のIDは空のまま、SQL実行の準備をする
$stmt = $pdo->prepare($sql);
// 更新する値と該当のIDを配列に格納する
$params = array($_POST['univcode'],$_POST['shikenshu'], $_POST['nendo'], $_POST['id']);
// 更新する値と該当のIDが入った変数をexecuteにセットしてSQLを実行
$stmt->execute($params);

//登録後、元の画面に戻る
header( "Location: upload.php" ) ;
?>