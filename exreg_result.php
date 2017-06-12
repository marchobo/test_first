<?php
require_once('session_check.php');
header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}


//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("userdb.php");
$dbh = db_connect();

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: registration_form.php");
	exit();
}
//POST情報を変数に登録
$account = $_POST['account'];
$data = $_POST['bikou'];

//パスワードのハッシュ化
$password_hash =  password_hash($_POST['password'], PASSWORD_DEFAULT);

//ここでデータベースに登録する
try{
	//例外処理を投げる（スロー）ようにする
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//トランザクション開始
	$dbh->beginTransaction();

	//memberテーブルに本登録する
	$statement = $dbh->prepare("INSERT INTO examiner (account,password,data) VALUES (:account,:password_hash,:data)");
	//プレースホルダへ実際の値を設定する
	$statement->bindValue(':account', $account, PDO::PARAM_STR);
	$statement->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
	$statement->bindValue(':data', $data, PDO::PARAM_STR);
	$statement->execute();

	// トランザクション完了（コミット）
	$dbh->commit();


}catch (PDOException $e){
	//トランザクション取り消し（ロールバック）
	$dbh->rollBack();
	$errors['error'] = "もう一度やりなおして下さい。";
	print('Error:'.$e->getMessage());
}

?>

<!DOCTYPE html>
<html>
<head>
<title>添削者登録完了</title>
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/regist.css">
<meta charset="utf-8">
</head>
<body>
<?php include('menu.html');?>

<?php if (count($errors) === 0): ?>
<div id="inputmathcode">
	<span style="font-size: x-large">添削者登録完了</span>
</div>

<p>登録完了いたしました。</p>

<?php
//登録後、元の画面に戻る
header( "Location: exregist.php" ) ;
?>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>

</body>
</html>