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
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/main.css">
<meta charset="utf-8">
<title>PDF変更</title>
<script src="js/checkfunc.js"></script>
</head>

<body>
<?php include('menu.html');?>
<div class="top_title">
	<span>復習の指針PDF変更画面</span>
</div>

<!--formのenctypeに"multipart/form-data"を設定する-->
<form action="pdfudresult.php" method="post" enctype="multipart/form-data">
<div class="input_data">
	<div class="inner_content">
	大学コード:
	<input type="text" size="5" name="univcode" value="<?= $_POST['univcode']?>" required>	試験種:
	<input type="text" size="5" name="shikenshu" style="width:30px;" value="<?= $_POST['shikenshu']?>" required>	年度:
	<input type="text" size="5" name="nendo" value="<?= $_POST['nendo']?>" required>	大問数:
	<input type="text" size="5" name="daimonsu" value="<?= $_POST['daimonsu']?>" required>
	</div>

	<!--input typeは"file"を設定する-->
	<div class="inner_content">
	<input type="file" name="upload">
	<input type="hidden" name="click" value="upload">
	<input type="hidden" name="id" value="<?=$_POST['id']?>">
	<input type="submit" value="変更">
	</div>
</div>
</form>

</body>
</html>