<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//前後にある半角全角スペースを削除する関数
function spaceTrim ($str) {
	// 行頭
	$str = preg_replace('/^[ 　]+/u', '', $str);
	// 末尾
	$str = preg_replace('/[ 　]+$/u', '', $str);
	return $str;
}

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: registration_form.php");
	exit();
}else{
	//POSTされたデータを各変数に入れる
	$account = isset($_POST['account']) ? $_POST['account'] : NULL;
	$password = isset($_POST['password']) ? $_POST['password'] : NULL;

	//前後にある半角全角スペースを削除
	$account = spaceTrim($account);
	$password = spaceTrim($password);

	//POSTされたデータを変数に入れる
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;

	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
		//データベース接続
		require_once("sqlconnect.php");
		$pdo = db_connect();
		//SQLで検索し、既存のデータではないか確認する
		$sql = "select * from member where mail = ?";
		$st = $pdo -> prepare($sql);
		$st->execute(array($mail));
		$count = $st->rowCount();
		if($count){
		 //ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
		 $errors['member_check'] = "このメールアドレスはすでに利用されております。";
		}
	}
	//アカウント入力判定
	if ($account == ''):
	$errors['account'] = "アカウントが入力されていません。";
	elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["account"])):
	$errors['account_length'] = "アカウントは半角英数字の5文字以上30文字以下で入力して下さい。";
	endif;

	//アカウントに被りがないか確認
	//データベース接続
	require_once("sqlconnect.php");
	$pdo = db_connect();
	//SQLで検索し、既存のデータではないか確認する
	$sql = "select * from member where account = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($account));
	$count = $st->rowCount();
	if($count){
		//ここで本登録用のmemberテーブルにすでに登録されているaccountかどうかをチェックする。
		$errors['account_check'] = "このアカウント名はすでに利用されております。";
	}

	//パスワード入力判定
	if ($password == ''):
	$errors['password'] = "パスワードが入力されていません。";
	elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password"])):
	$errors['password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力して下さい。";
	else:
	$password_hide = str_repeat('*', strlen($password));
	endif;

}

//エラーが無ければセッションに登録
if(count($errors) === 0){
	$_SESSION['account'] = $account;
	$_SESSION['password'] = $password;
	$_SESSION['mail'] = $mail;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>会員登録確認画面</title>
<meta charset="utf-8">
</head>
<body>
<h1>会員登録確認画面</h1>

<?php if (count($errors) === 0): ?>


<form action="registration_insert.php" method="post">

<p>メールアドレス：<?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></p>
<p>アカウント名：<?=htmlspecialchars($account, ENT_QUOTES)?></p>
<p>パスワード：<?=$password_hide?></p>

<input type="button" value="戻る" onClick="history.back()">
<input type="hidden" name="token" value="<?=$_POST['token']?>">
<input type="submit" value="登録する">

</form>

<?php elseif(count($errors) > 0): ?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<input type="button" value="戻る" onClick="history.back()">

<?php endif; ?>

</body>
</html>