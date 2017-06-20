<?php
require_once('session_check.php');
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/main.css">
<meta charset="utf-8">
<title>PDFアップロード</title>
<script src="js/checkfunc.js"></script>
</head>

<body>
<?php include('menu.html');?>
<div class="top_title">
	<span>復習の指針PDFアップロード</span>
</div>

<!--formのenctypeに"multipart/form-data"を設定する-->
<form action="upresult.php" method="post" enctype="multipart/form-data">
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
<hr />
	<div class="content_title">
		<span>登録済みPDF一覧</span>
	</div>
	<?php
	require_once('db/sqlconnect.php');
	$pdo = db_connect();
	$sql = "SELECT * FROM pdf ORDER BY univcode, shikenshu, nendo";
	$stmt = $pdo->query($sql);
	?>
	<div>
	<table class="main_table">
	<tr>
		<th>大学コード</th>
		<th>試験種</th>
		<th>年度</th>
		<th>大問数</th>
		<th>変更</th>
		<th>削除</th>
		<th>加点要素登録</th>
		<th>位置登録</th>
		<th>試験情報登録</th>
		<th>登録済（加点要素）</th>
		<th>登録済（他項目）</th>
		<th>登録済（画像）</th>
	</tr>
	<?php
	foreach ($stmt as $row) {
	?>
		<tr>
			<td><?php echo $row['univcode']; ?></td>
			<td><?php echo $row['shikenshu']; ?></td>
			<td><?php echo $row['nendo']; ?></td>
			<td><?php echo $row['daimonsu'];
			$sql = "select * from shikendata where pdfid = ?";
			$st = $pdo -> prepare($sql);
			$st->execute(array($row['id']));
			$countk = $st->rowCount();
			if($row['daimonsu'] != $countk){
				echo "*";
			}

			?></td>
			<td>
				<form action="pdfupdate.php" method="post">
					<input type="submit" value="変更する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="univcode" value="<?=$row['univcode']?>">
					<input type="hidden" name="shikenshu" value="<?=$row['shikenshu']?>">
					<input type="hidden" name="nendo" value="<?=$row['nendo']?>">
					<input type="hidden" name="daimonsu" value="<?=$row['daimonsu']?>">
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
			<td>
				<form action="shikendata.php" method="post">
					<input type="submit" value="試験情報を登録する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="click" value="shikendata">
					<input type="hidden" name="daimonsu" value="<?=$row['daimonsu']?>">
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
			<td><?php
			$sql = "select * from subitem";
			$st = $pdo -> query($sql);
			$countk = $st->rowCount();
			$sql = "select * from pdfpos_sub where pdfid = ?";
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
			<td><?php
			$sql = "select * from imgitem";
			$st = $pdo -> query($sql);
			$countk = $st->rowCount();
			$sql = "select * from pdfpos_img where pdfid = ?";
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