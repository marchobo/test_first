<?php
require_once('session_check.php');
//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}
$token = $_SESSION['token'];
?>

<!doctype html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="../css/menu.css">
<link rel="stylesheet" type="text/css" href="../css/main.css">
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
<meta charset="utf-8">
<title>点数入力</title>
</head>

<body>
<?php include('menu.html');
require_once('../db/sqlconnect.php');
$pdo = db_connect();
//入力情報から変数へ
$exid = $_POST['exid'];
$koza = substr($_POST['pdfname'],0,2);
$univcode = substr($_POST['pdfname'],2,4);
$shikenshu = substr($_POST['pdfname'],6,2);
$shikenshu_sub = substr($_POST['pdfname'],7,1);
$nendo_sub = substr($_POST['pdfname'],8,2);
$nendo = '20'.$nendo_sub;
$stuid = substr($_POST['pdfname'],10,8);
$kaisu = $_POST['kaisu'];
$hanigai = 0;

//大学コードがなければエラー
$sql = "select * from univname where univcode = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($univcode));
$countk = $st->rowCount();
if($countk != 1){
	?>
	<br>
	<?php
	die('エラー：不正なpdf名です。');
}
//大学名を一応取得
foreach($st as $row){
	$univname = $row['univname'];
}
?>
<div class="top_title">
	<span>復習の指針点数入力</span>
</div>
<div class="input_data">
ID:<?= $exid?>	生徒番号:<?= $stuid?>	〈<?= $univname?><?= $nendo?>年度	数学<?php if($shikenshu_sub){echo $shikenshu_sub;}?>	<?= $kaisu?>回目〉
</div>
<div>
	<div class="content_title">大問得点入力</div>
	<form action="shishin_result.php" method="post">
	<table class="main_table">
	<tr>
	<?php
	$pdo = db_connect();
	$sql = "SELECT * FROM shikendata WHERE pdfid IN (SELECT id FROM pdf where univcode = ? and shikenshu = ? and nendo = ?) ORDER BY daimon";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($univcode, $shikenshu, $nendo));
	foreach ($stmt as $row) {
	?>
		<th>大問<?= $row['daimon']?></th>
	<?php
	}
	?>
	</tr>
	<tr>
	<?php
	$pdo = db_connect();
	$sql = "SELECT * FROM shikendata WHERE pdfid IN (SELECT id FROM pdf where univcode = ? and shikenshu = ? and nendo = ?) ORDER BY daimon";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($univcode, $shikenshu, $nendo));
	foreach ($stmt as $row) {
		$pdfid = $row['pdfid'];
	?>
		<td>
		<?php if($row['kaitou']){?>
		<input type="number" style="width:60px;" name="daimon[<?=$row['daimon']?>]" min="0" max="<?=$row['manten']?>" required>
		<?php
		echo '/'.$row['manten'];
		}
		else{
			echo $row['manten'].'/'.$row['manten'];
			$hanigai += $row['manten'];
			?>
			<input type="hidden" name="daimon[<?=$row['daimon']?>]" value="<?=$row['manten']?>">
		<?php }?>
		<input type="hidden" name="hanigai" value="<?=$hanigai?>">
		</td>
	<?php
	}
	//大問数を取得したい
	$sql = "SELECT * FROM pdf where univcode = ? and shikenshu = ? and nendo = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($univcode, $shikenshu, $nendo));
	foreach ($stmt as $row) {
		$daimonsu = $row['daimonsu'];
	}
	?>
	</tr>
	</table>
	<hr />

	復習の指針入力<br>
	<table class="main_table">
	<?php

	$pdo = db_connect();
	$sql = "SELECT * FROM koumoku WHERE pdfid = ? ORDER BY daimon, shomon, junban";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($pdfid));
	?>
	<tr>
		<th>大問</th>
		<th>小問</th>
		<th>加点要素</th>
		<th>配点</th>
		<th>ランク</th>
		<th>取りこぼし</th>
	</tr>
	<?php
	//前行の大問・小問番号保存用
	$d = -1;
	$d_num=0;//大問の加点要素の個数
	$d_change = 0;//大問が変わったフラグ
	$s = -1;
	$s_num=0;//小問の加点要素の個数
	foreach ($stmt as $row) {
	?>
		<tr>
		<?php if($row['daimon']!=$d){
			//大問番号ごとに、セルを結合する
			$d_change = 1;
			$d=$row['daimon'];
			$sql_tmp = "SELECT * FROM koumoku WHERE pdfid = ? and daimon = ?";
			$stmt_tmp = $pdo->prepare($sql_tmp);
			$stmt_tmp->execute(array($pdfid, $d));
			$d_num = $stmt_tmp->rowCount();?>
			<td rowspan="<?=$d_num?>"><?=$d; ?></td>
		<?php
		}?>
		<?php if($row['shomon'] != $s||$d_change != 0){
			//小問番号ごとに、セルを結合する
			$s = $row['shomon'];
			$sql_tmp = "SELECT * FROM koumoku WHERE pdfid = ? and daimon = ? and shomon = ?";
			$stmt_tmp = $pdo->prepare($sql_tmp);
			$stmt_tmp->execute(array($pdfid, $d, $s));
			$s_num = $stmt_tmp->rowCount();?>
			<td rowspan="<?=$s_num?>"><?php if($s){echo '('.$s.')';} ?></td>
		<?php
		}?>
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
		<td><input type="number" style="width:60px;" name="koboshi[<?=$row['id']?>]" min="0" max="<?=$row['haiten']?>" required>/<?=$row['haiten']?></td>
		</tr>
	<?php
	$d_change = 0;//戻しておく
	}
	?>
	</table>
	<input type="hidden" name="pdfname" value="<?=$_POST['pdfname']?>">
	<input type="hidden" name="koza" value="<?=$koza?>">
	<input type="hidden" name="pdfid" value="<?=$pdfid?>">
	<input type="hidden" name="exid" value="<?=$exid?>">
	<input type="hidden" name="stuid" value="<?=$stuid?>">
	<input type="hidden" name="univcode" value="<?=$univcode?>">
	<input type="hidden" name="shikenshu" value="<?=$shikenshu?>">
	<input type="hidden" name="nendo" value="<?=$nendo?>">
	<input type="hidden" name="kaisu" value="<?=$kaisu?>">
	<input type="hidden" name="daimonsu" value="<?=$daimonsu?>">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="submit" value="復習の指針PDF出力" />
	</form>
</div>
</body>
</html>