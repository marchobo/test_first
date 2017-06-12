<?php
require_once('session_check.php');
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="../css/shishin_input.css">
<meta charset="utf-8">
<title>点数入力</title>
</head>

<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">復習の指針点数入力</span>
</div>
<div id="input">
<form method="post" action="shishin_input.php">
スタッフID（例 N0123456）:
<input type="text" style="width:30px;" name="exid" required>	PDF名（18桁）:
<input type="number" style="width:50px;" name="pdfname" style="width:30px;" required>	回数:
<input type="number" style="width:10px;" name="kaisu" required>回目

<input type="submit" value="項目に登録" />
</form>
</div>
</body>
</html>