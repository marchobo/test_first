<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="regist.css">
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

<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>

<script>
$(function() {
function makePreview() {
input = $('#mathcode').val().replace(/</g, "&lt;").replace(/>/g, "&gt;");
$('#preview').html(input);
MathJax.Hub.Queue(["Typeset",MathJax.Hub,"preview"]);
}
$('body').keyup(function(){makePreview()});
$('body').bind('updated',function(){makePreview()});
makePreview();
});
</script>

<meta charset="UTF-8">
<title>項目登録画面</title>
</head>
<body>
	<div id="inputmathcode">
		<form method="post" action="result.php">
			項目のコード入力（行内で複数行の数式を書く場合：\displaystyle）<br>
			<textarea name="mathcode" id="mathcode" rows="5" cols="100" wrap="soft"></textarea><br>
			入力プレビュー<br>
			<div id="preview"></div>
			大問番号：
			<input type="text" size="5" name="daimon" >	小問番号：
			<input type="text" size="5" name="shomon" >	配点：
			<input type="text" size="5" name="haiten" >
			<input type="submit" value="項目に登録" />
		</form>
	</div>
	<hr />
	<div id="mathview">
	登録済み項目<br>
	<?php
	$pdo = new PDO("mysql:host=localhost;dbname=db_test_1", "root", 't873n338');
	$sql = "SELECT * FROM hukushus16 ORDER BY daimon, shomon";
	$stmt = $pdo->query($sql);
	foreach ($stmt as $row) {
		// データベースのフィールド名で出力
		echo '大問'.$row['daimon'].' 小問'.$row['shomon'].' '.$row['katen'].' '.$row['haiten'];
		// 改行を入れる
		echo '<br>';
	}
	?>
	</div>
</body>
</html>