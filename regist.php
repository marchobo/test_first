<?php
require_once('session_check.php');
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'regist') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	if(isset($_SESSION['click'])){
		if($_SESSION['click']!='regist'){
			die('エラー：正しくアクセスしてください。');
		}
		else{
			//セッションの初期化
			$_SESSION['click']='';
		}
	}
	else{
		die('エラー：セッションが設定されていません。');
	}
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

<script src="js/checkfunc.js"></script>

<script async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-AMS_CHTML"></script>
<script src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
<script src="js/makepreview.js"></script>

<meta charset="UTF-8">
<title>項目登録画面</title>
</head>
<body>
	<?php include('menu.html');?>
	<br>
	<?php
		if(isset($_POST['univcode'])){
			if(isset($_POST['shikenshu'])){
				if(isset($_POST['nendo'])){
					$_SESSION['univcode']=$_POST['univcode'];
					$_SESSION['shikenshu']=$_POST['shikenshu'];
					$_SESSION['nendo']=$_POST['nendo'];
				}
			}
		}
		require_once('db/sqlconnect.php');
		$pdo = db_connect();
		$sql = "select * from pdf where univcode = ? and shikenshu = ? and nendo = ?";
		$st = $pdo -> prepare($sql);
		$st->execute(array($_SESSION['univcode'],$_SESSION['shikenshu'], $_SESSION['nendo']));
		$count = $st->rowCount();
		if ($count == 0){
			$pdo = null;
			die('そのPDFデータは登録されていません。');
		}
		else{
			foreach($st as $row)
			$pdfid = $row['id'];
		}
	?>
	<div class="top_title">
		<span>復習の指針項目登録（<?= $_SESSION['univcode'].'/'.$_SESSION['shikenshu'].'/'.$_SESSION['nendo']?>）</span>
	</div>
	<div>
		<div class="content_title">項目のコード入力（行内で複数行の数式を書く場合：\displaystyle）</div>
		<form method="post" action="result.php">
			<textarea name="mathcode" id="mathcode" rows="2" cols="100" wrap="soft" required></textarea><br>
			<div class="content_title">入力プレビュー</div>
			<div id="preview"></div>
			大問番号：
			<input type="number" style="width:50px;" name="daimon" required>	小問番号：
			<input type="number" style="width:50px;" name="shomon" required>	小問内での順番：
			<input type="number" style="width:50px;" name="junban" required>	配点：
			<input type="number" style="width:50px;" name="haiten" required>	ランク：
			<select  name="rank" required>
				<option></option>
				<option value="0">X</option>
				<option value="1">A</option>
				<option value="2">B</option>
			</select>
			<input type="hidden" name="click" value="regist">
			<input type="hidden" name="pdfid" value="<?=$pdfid ?>">
			<input type="hidden" name="univcode" value="<?=$_SESSION['univcode'] ?>">
			<input type="hidden" name="shikenshu" value="<?=$_SESSION['shikenshu']?>">
			<input type="hidden" name="nendo" value="<?=$_SESSION['nendo']?>">
			<input type="submit" value="項目に登録" />
		</form>
	</div>
	<hr />
	<div class="content_title">登録済み項目</div>
	<?php
	require_once('db/sqlconnect.php');
	$pdo = db_connect();
	$sql = "SELECT * FROM koumoku WHERE pdfid = ? ORDER BY daimon, shomon, junban";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($pdfid));
	?>
	<table class="main_table">
	<tr>
		<th>大問</th>
		<th>小問</th>
		<th>順番</th>
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
			<td><?php echo $row['junban']; ?></td>
			<td class ="koumoku"><?php echo $row['koumoku']; ?></td>
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
					<input type="hidden" name="daimon" value="<?=$row['daimon']?>">
					<input type="hidden" name="shomon" value="<?=$row['shomon']?>">
					<input type="hidden" name="junban" value="<?=$row['junban']?>">
					<input type="hidden" name="koumoku" value="<?=$row['koumoku']?>">
					<input type="hidden" name="haiten" value="<?=$row['haiten']?>">
					<input type="hidden" name="rank" value="<?=$row['rank']?>">
					<input type="hidden" name="univcode" value="<?=$_SESSION['univcode'] ?>">
					<input type="hidden" name="shikenshu" value="<?=$_SESSION['shikenshu']?>">
					<input type="hidden" name="nendo" value="<?=$_SESSION['nendo']?>">
					<input type="hidden" name="click" value="update">
				</form>
			</td>
			<td>
				<form action="delete.php" method="post" onSubmit="return check()">
					<input type="submit" value="削除する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="univcode" value="<?=$_SESSION['univcode'] ?>">
					<input type="hidden" name="shikenshu" value="<?=$_SESSION['shikenshu']?>">
					<input type="hidden" name="nendo" value="<?=$_SESSION['nendo']?>">
					<input type="hidden" name="click" value="delete">
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