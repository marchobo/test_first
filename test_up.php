<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/main.css">
<meta charset="utf-8">
<title>答案登録</title>
<script src="js/checkfunc.js"></script>
</head>

<body>
<?php include('menu.html');?>
<div class="top_title">
	<span>答案PDF・情報登録</span>
</div>

<!--formのenctypeに"multipart/form-data"を設定する-->
<form action="test.php" method="post" enctype="multipart/form-data">
<div class="input_data">
	<div class="inner_content">
	大学コード:
	<input type="number" style="width:50px;" name="univcode" required>	試験種:
	<input type="number" style="width:50px;" name="shikenshu" style="width:30px;" required>	年度:
	<input type="number" style="width:50px;" name="nendo" required>	大問数:
	<input type="number" style="width:50px;" name="daimonsu" required>
	</div>

	<!--input typeは"file"を設定する-->
	<div class="inner_content">
	<input type="file" name="upload">
	<input type="hidden" name="click" value="upload">
	<input type="submit" value="アップロード">
	</div>
</div>
</form>
</body>
</html>