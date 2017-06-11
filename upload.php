<?php
require_once('session_check.php');
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
<form action="upresult.php" method="post" enctype="multipart/form-data">
<div id="upload">
	<div id="updata">
	大学コード:
	<input type="text" size="5" name="univcode" required>	試験種:
	<input type="text" size="5" name="shikenshu" style="width:30px;" required>	年度:
	<input type="text" size="5" name="nendo" required>
	</div>

	<!--input typeは"file"を設定する-->
	<div id="upfile">
	<input type="file" name="upload">
	<input type="hidden" name="click" value="upload">
	<input type="submit" value="アップロード">
	</div>
</div>
</form>
<hr />
	<div id="mathview">
	登録済みPDF一覧<br>
	<?php
	require_once('sqlconnect.php');
	$pdo = db_connect();
	$sql = "SELECT * FROM pdf ORDER BY univcode, shikenshu, nendo";
	$stmt = $pdo->query($sql);
	?>
	<table id="hv_table">
	<tr>
		<th>大学コード</th>
		<th>試験種</th>
		<th>年度</th>
		<th>変更</th>
		<th>削除</th>
		<th>項目登録</th>
		<th>位置登録</th>
		<th>登録済件数</th>
	</tr>
	<?php
	foreach ($stmt as $row) {
	?>
		<tr>
			<td><?php echo $row['univcode']; ?></td>
			<td><?php echo $row['shikenshu']; ?></td>
			<td><?php echo $row['nendo']; ?></td>
			<td>
				<form action="pdfupdate.php" method="post">
					<input type="submit" value="変更する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="univcode" value="<?=$row['univcode']?>">
					<input type="hidden" name="shikenshu" value="<?=$row['shikenshu']?>">
					<input type="hidden" name="nendo" value="<?=$row['nendo']?>">
					<input type="hidden" name="click" value="pdfupdate">
				</form>
			</td>
			<td>
				<form action="pdfdelete.php" method="post" onSubmit="return check()">
					<input type="submit" value="削除する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="click" value="pdfdelete">
				</form>
			</td>
			<td>
				<form action="regist.php" method="post">
					<input type="submit" value="項目を登録する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="univcode" value="<?=$row['univcode']?>">
					<input type="hidden" name="shikenshu" value="<?=$row['shikenshu']?>">
					<input type="hidden" name="nendo" value="<?=$row['nendo']?>">
					<input type="hidden" name="click" value="regist">
				</form>
			</td>
			<td>
				<form action="posreg.php" method="post">
					<input type="submit" value="位置を登録する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="click" value="posreg">
				</form>
			</td>
			<td><?php
			$sql = "select * from koumoku where pdfid = ?";
			$st = $pdo -> prepare($sql);
			$st->execute(array($row['id']));
			$countk = $st->rowCount();
			$sql = "select * from pdfpos where pdfid = ?";
			$st = $pdo -> prepare($sql);
			$st->execute(array($row['id']));
			$countp = $st->rowCount();
			echo $countp.'/'.$countk;
			if($countk!=0){
				if($countp==$countk){
					echo '(完了)';
				}
			}
			?></td>
		</tr>
	<?php
	}
	?>
	</table>
	</div>
</body>
</html>