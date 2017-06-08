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
<script src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
<script src="makepreview.js"></script>

<meta charset="UTF-8">
<title>項目登録画面</title>
</head>
<body>
	<div id="inputmathcode">
		<span style="font-size:x-large">復習の指針項目登録</span>
		<form method="post" action="result.php">
			項目のコード入力（行内で複数行の数式を書く場合：\displaystyle）<br>
			<textarea name="mathcode" id="mathcode" rows="2" cols="100" wrap="soft" required></textarea><br>
			入力プレビュー<br>
			<div id="preview"></div>
			大問番号：
			<input type="text" size="5" name="daimon" required>	小問番号：
			<input type="text" size="5" name="shomon" required>	配点：
			<input type="text" size="5" name="haiten" required>	ランク：
			<select  name="rank" required>
				<option></option>
				<option value="0">X</option>
				<option value="1">A</option>
				<option value="2">B</option>
			</select>
			<input type="submit" value="項目に登録" />
		</form>
	</div>
	<hr />
	<div id="mathview">
	登録済み項目<br>
	<?php
	require_once('sqlconnect.php');
	$pdo = db_connect();
	$sql = "SELECT * FROM koumoku ORDER BY daimon, shomon";
	$stmt = $pdo->query($sql);
	?>
	<table id="hv_table">
	<tr>
		<th>大問</th>
		<th>小問</th>
		<th>項目</th>
		<th>配点</th>
		<th>ランク</th>
		<th>変更</th>
		<th>削除</th>
	</tr>
	<?php
	foreach ($stmt as $row) {
	?>
		<tr>
			<td><?php echo $row['daimon']; ?></td>
			<td><?php echo $row['shomon']; ?></td>
			<td id ="koumoku"><?php echo $row['koumoku']; ?></td>
			<td><?php echo $row['haiten']; ?></td>
			<td><?php
			switch($row['rank']){
				case 0:
					echo "X";
					break;
				case 1:
					echo "A";
					break;
				case 2:
					echo "B";
					break;
			}?></td>
			<td>
				<form action="update.php" method="post">
					<input type="submit" value="変更する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
				</form>
			</td>
			<td>
				<form action="delete.php" method="post">
					<input type="submit" value="削除する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
				</form>
			</td>
		</tr>
	<?php
	}
	?>
	</table>
	</div>
</body>
</html>