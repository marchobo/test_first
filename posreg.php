<?php
require_once('session_check.php');
//画面が戻って来た場合の対応
if(isset($_POST['id'])){
	$_SESSION['pdfid']=$_POST['id'];
}
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'posreg') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	if(isset($_SESSION['click'])){
		if($_SESSION['click']!='posreg'){
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

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/regist.css">
<meta charset="utf-8">
<title>PDFアップロード</title>
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
<script src="js/checkfunc.js"></script>
</head>

<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">復習の指針PDFセル位置設定</span>
</div>
<div id="mathview">
	セル位置一覧<br>
	<?php
	require_once('db/sqlconnect.php');
	$pdo = db_connect();
	$sql = "SELECT * FROM koumoku WHERE pdfid = ? ORDER BY daimon, shomon";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($_SESSION['pdfid']));
	?>

	<form action="posregresult.php" method="post">
	<table id="hv_table">
	<tr>
		<th>大問</th>
		<th>小問</th>
		<th>順番</th>
		<th>項目</th>
		<th>配点</th>
		<th>ランク</th>
		<th>位置X</th>
		<th>位置Y</th>
	</tr>
	<?php
		foreach ($stmt as $row) {
	?>
	<tr>
		<td><?php echo $row['daimon']; ?></td>
		<td><?php echo $row['shomon']; ?></td>
		<td><?php echo $row['junban']; ?></td>
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
		<?php
		//posx,posyデータがあれば取得
		$flag = 0;//データが既にあったかどうか
		$posx=null;
		$posy=null;
		$sql = "SELECT * FROM pdfpos WHERE id = ?";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array($row['id']));
		$count = $stmt->rowCount();
		if ($count != 0){
			$flag = 1;
			foreach($stmt as $row2){
				$posx = $row2['posx'];
				$posy = $row2['posy'];
			}
		}
		?>

		<td>
			<input type="number" name="posx[]" value="<?php if($flag){echo $posx;}?>" style="width:100px;" step="0.1" required>
		</td>
		<td>
			<input type="number" name="posy[]" value="<?php if($flag){echo $posy;}?>" style="width:100px;" step="0.1" required>
		</td>
	</tr>
	<input type="hidden" name="id[]" value="<?=$row['id']?>">
	<input type="hidden" name="flag[]" value="<?=$flag ?>">

	<?php
	}
	?>
	</table>
	</div>
	<table>
		<tr>
			<td>
				<input type="submit" value="確定する">
				<input type="hidden" name="click" value="posreg">
				<input type="hidden" name="pdfid" value="<?=$_SESSION['pdfid'] ?>">
			</form>
			</td>
			<td>
				<form action="pospreview.php" method="post">
					<input type="submit" value="プレビューをダウンロード">
				</form>
			<td>
		</tr>
	</table>
</body>
</html>