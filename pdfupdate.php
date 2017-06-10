<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'pdfupdate') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「変更する」をクリックしてください。');
}
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/upload.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/regist.css">
<meta charset="utf-8">
<title>PDFアップロード</title>
<script src="js/checkfunc.js"></script>
</head>

<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">復習の指針PDFアップロード</span>
</div>

<!--formのenctypeに"multipart/form-data"を設定する-->
<form action="pdfudresult.php" method="post" enctype="multipart/form-data">
<div id="upload">
	<div id="updata">
	大学コード:
	<input type="text" size="5" name="univcode" value="<?= $_POST['univcode']?>" required>	試験種:
	<input type="text" size="5" name="shikenshu" style="width:30px;" value="<?= $_POST['shikenshu']?>" required>	年度:
	<input type="text" size="5" name="nendo" value="<?= $_POST['nendo']?>" required>
	</div>

	<!--input typeは"file"を設定する-->
	<div id="upfile">
	<input type="file" name="upload">
	<input type="hidden" name="click" value="upload">
	<input type="hidden" name="id" value="<?=$_POST['id']?>">
	<input type="submit" value="変更">
	</div>
</div>
</form>

</body>
</html>