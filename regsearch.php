<?php
require_once('session_check.php');
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/upload.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">
<meta charset="utf-8">
<title>PDF検索</title>
</head>

<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">復習の指針PDF検索</span>
</div>

<!--formのenctypeに"multipart/form-data"を設定する-->
<form action="regist.php" method="post">
<div id="upload">
	<div id="updata">
	大学コード:
	<input type="number" style="width:50px;" size="5" name="univcode" required>	試験種:
	<input type="number" style="width:50px;" name="shikenshu" style="width:30px;" required>	年度:
	<input type="number" style="width:50px;" name="nendo" required>
	<input type="hidden" name="click" value="regist">
	</div>

	<div id="upfile">
	<input type="submit" value="検索">
	</div>
</div>
</form>

</body>
</html>