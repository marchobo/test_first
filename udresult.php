<?php
//データ更新用
require_once('session_check.php');
require_once('db/sqlconnect.php');
$pdo = db_connect();
// UPDATE文を変数に格納
$sql = "UPDATE koumoku SET daimon = :daimon, shomon = :shomon, junban = :junban , koumoku = :koumoku, haiten = :haiten, rank = :rank WHERE id = :id";

// 更新する値と該当のIDは空のまま、SQL実行の準備をする
$stmt = $pdo->prepare($sql);

// 更新する値と該当のIDを配列に格納する
$params = array(':daimon' => $_POST['daimon'], ':shomon' => $_POST['shomon'], ':junban' => $_POST['junban'], ':koumoku' => $_POST['mathcode'], ':haiten' => $_POST['haiten'], ':rank' => $_POST['rank'], ':id' => $_POST['id']);
// 更新する値と該当のIDが入った変数をexecuteにセットしてSQLを実行
$stmt->execute($params);

//登録後、元の画面に戻る
$_SESSION['click']='regist';
header( "Location: regist.php" ) ;
?>