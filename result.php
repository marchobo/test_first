<?php
//MYSQLでデータベースにPOSTデータを登録
$pdo = new PDO("mysql:host=localhost;dbname=db_test_1", "root", 't873n338');
$st = $pdo -> prepare("INSERT INTO hukushuS16 VALUES(?, ?, ?, ?, ?)");
$st->execute(array(1, $_POST['daimon'],$_POST['shomon'], $_POST['mathcode'],$_POST['haiten']));

//登録後、元の画面に戻る
header( "Location: regist.php" ) ;
?>
