<?php
require_once('session_check.php');
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/main.css">

<meta charset="utf-8">
<title>データ管理</title>
</head>

<body>
<?php include('menu.html');?>
<div class="top_title">
	<span>データ管理画面</span>
</div>
<table class = "main_table">
<tr>
	<td>大問得点データをCSV形式でダウンロード</td>
	<td>
		<form action="downloaddata.php" method="post">
			<input type="hidden" name="click" value="csvdown">
			<input type="hidden" name="datatype" value="1">
			<input type="submit" value="ダウンロード" />
		</form>
	</td>
</tr>
<tr>
	<td>復習の指針データをCSV形式でダウンロード</td>
	<td>
		<form action="downloaddata.php" method="post">
			<input type="hidden" name="click" value="csvdown">
			<input type="hidden" name="datatype" value="2">
			<input type="submit" value="ダウンロード" />
		</form>
	</td>
</tr>
</table>
</body>
</html>
