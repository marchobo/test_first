<?php
require_once('session_check.php');
//画面が戻って来た場合の対応
if(isset($_POST['id'])){
	$_SESSION['pdfid']=$_POST['id'];
}
if(isset($_POST['daimonsu'])){
	$_SESSION['daimonsu']=$_POST['daimonsu'];
}
//直打ち禁止
if(isset($_POST['click'])){
	if ($_POST['click'] != 'shikendata') {
		die('エラー：不正アクセスの可能性があります。');
	}
}
else{
	if(isset($_SESSION['click'])){
		if($_SESSION['click']!='shikendata'){
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
<title>試験情報登録</title>
<script src="js/checkfunc.js"></script>
</head>

<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">試験情報登録</span>
</div>
<div id="mathview">
	満点・解答可否（可:1 否:0）を登録してください。<br>
	<?php
	require_once('db/sqlconnect.php');
	$pdo = db_connect();
	?>
	<table id="hv_table">
	<tr>
		<th>大問</th>
		<th>満点</th>
		<th>解答可否</th>
	</tr>
	<form action="shikendt_result.php" method="post">
	<?php
		for ($i=0; $i<$_SESSION['daimonsu']; $i++) {
		?>
			<tr>
				<td><?php echo $i+1; ?></td>
				<?php
				//manten,kaitouデータがあれば取得
				$flag = 0;//データが既にあったかどうか
				$manten=null;
				$kaitou=null;
				$sql = "SELECT * FROM shikendata WHERE pdfid = ? and daimon = ?";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($_SESSION['pdfid'], $i+1));
				$count = $stmt->rowCount();
				if ($count != 0){
					$flag = 1;
					foreach($stmt as $row2){
						$manten = $row2['manten'];
						$kaitou = $row2['kaitou'];
					}
				}
				?>

				<td>
					<input type="number" name="manten[]" value="<?php if($flag){echo $manten;}?>" style="width:100px;" required>
				</td>
				<td>
					<input type="number" name="kaitou[]" value="<?php if($flag){echo $kaitou;}?>" min = "0" max="1" style="width:100px;" required>
				</td>

			</tr>
			<input type="hidden" name="flag[]" value="<?= $flag ?>">
		<?php
		}
		?>
	</table>
	<input type="submit" value="確定する">
	<input type="hidden" name="click" value="shikendata">
	<input type="hidden" name="pdfid" value="<?=$_SESSION['pdfid'] ?>">
	<input type="hidden" name="daimonsu" value="<?=$_SESSION['daimonsu'] ?>">
	</form>
	</div>

</body>
</html>