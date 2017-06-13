<?php
require_once('session_check.php');
//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="../css/shishin.css">
<meta charset="utf-8">
<title>情報入力</title>
</head>

<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">復習の指針情報入力</span>
</div>

<div id="input">
<form method="post" action="shishin_input.php">
<p>スタッフID（例 N0123456）:<input type="text" style="width:150px;" name="exid" pattern="N0+\d{6}" required></p>
<p>PDF名（18桁）:<input type="text" style="width:250px;" name="pdfname" pattern="0+\d{17}" required></p>
<p>回数:<input type="number" style="width:100px;" name="kaisu" min="1" max="4" required>回目</p>
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="点数入力画面へ" />
</form>
</div>
</body>
</html>