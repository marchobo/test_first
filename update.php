<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'update') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	die('エラー：「変更する」をクリックしてください。');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">

<script type="text/x-mathjax-config">
MathJax.Hub.Config({
  tex2jax: {
    inlineMath: [['$','$'], ['\\(','\\)']],
    processEscapes: true
  },
  CommonHTML: { matchFontHeight: false },
  displayAlign: "left",
  displayIndent: "2em"
});
</script>
<script async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-AMS_CHTML"></script>
<script src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
<script src="js/makepreview.js"></script>

<meta charset="UTF-8">
<title>項目変更画面</title>
</head>
<body>
	<?php include('menu.html');?>
	<div class="top_title">
		<span>復習の指針項目変更</span>
	</div>
	<div>
		<form method="post" action="udresult.php">
			<div class="content_title">
				項目のコード入力（行内で複数行の数式を書く場合：\displaystyle）
			</div>
			<textarea name="mathcode" id="mathcode" rows="2" cols="100" wrap="soft" required><?= $_POST['koumoku']?></textarea><br>
			入力プレビュー<br>
			<div id="preview"></div>
			大問番号：
			<input type="number" style="width:50px;" name="daimon" value="<?= $_POST['daimon']?>" required>	小問番号：
			<input type="number" style="width:50px;" name="shomon" value="<?= $_POST['shomon']?>" required>	小問内での順番：
			<input type="number" style="width:50px;" name="junban" value="<?= $_POST['junban']?>" required>	配点：
			<input type="number" style="width:50px;" name="haiten" value="<?= $_POST['haiten']?>" required>	ランク：
			<select  name="rank" required>
				<option></option>
				<option value="0" <?php if($_POST["rank"] == "0"){print " selected";} ?>>X</option>
				<option value="1" <?php if($_POST["rank"] == "1"){print " selected";} ?>>A</option>
				<option value="2" <?php if($_POST["rank"] == "2"){print " selected";} ?>>B</option>
			</select>
			<input type="hidden" name="id" value="<?=$_POST['id']?>">
			<input type="hidden" name="univcode" value="<?=$_POST['univcode'] ?>">
			<input type="hidden" name="shikenshu" value="<?=$_POST['shikenshu']?>">
			<input type="hidden" name="nendo" value="<?=$_POST['nendo']?>">
			<input type="submit" value="変更を登録" />
		</form>
	</div>
</body>
</html>