<?php
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'csvdown') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「ダウンロード」をクリックしてください。');
}
//データDL用
require_once('session_check.php');
require_once('db/sqlconnect.php');

//SQLに接続
$pdo = db_connect();

//クリックしたボタンに対応したTABLEを選択
switch($_POST['datatype']){
	case 1:
		$sql = "select * from tensudata";
		$sql_f = "describe tensudata";
		$st = $pdo -> query($sql);
		$st_f = $pdo -> query($sql_f);
		$file = 'tensudata.csv';
		break;
	case 2:
		$sql = "select * from hsdata";
		$sql_f = "describe hsdata";
		$st = $pdo -> query($sql);
		$st_f = $pdo -> query($sql_f);
		$file = 'hsdata.csv';
		break;
	default:
		die();
}

//ここからCSV出力へ
header('Content-Disposition: attachment; filename='.$file);
header('Content-Type: text/csv;');

//出力バッファーをファイルポインターとして扱うことができる
$stream = fopen('php://output', 'w');

//$stのフィールド名を、CSVの1行目として出力
foreach($st_f as $value){
	$field_names[] = $value[0];
}
_fputcsv($stream, $field_names);

//データの中身を出力
foreach ($st as $rows) {
	$row = null;
	foreach($field_names as $key => $field_name){
		$row[] = $rows[$field_name];
	}
	_fputcsv($stream, $row);
}

//fputcsvはクオートをつけて出力できないので、新たな関数を定義
function _fputcsv($fp, $fields) {
	$tmp = array();
	foreach ($fields as $value) {
		$value = str_replace('"', '""', $value);
		$tmp[]= '"'.$value.'"';
	}
	$str = implode(',', $tmp);
	$str .= "\n";
	fputs($fp, $str);
}
?>