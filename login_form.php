<?php
session_start();
//クリックをセッション管理
$_SESSION['click'] = '';
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>ログイン画面</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>

<div class="content">
<span style="font-size:x-large">復習の指針管理システム</span>

<form action="login_check.php" method="post">

<table>
	<tr>
		<td>ID：</td>
		<td><input type="text" name="account" size="30"></td>
	</tr>
	<tr>
		<td>パスワード：</td>
		<td><input type="password" name="password" size="30"></td>
	</tr>
</table>

<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="ログインする">

</form>
</div>
</body>
</html>