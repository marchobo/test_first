<?php
require_once('session_check.php');


header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
$token = $_SESSION['token'];

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

?>

<!DOCTYPE html>
<html>
<head>
<title>添削者登録画面</title>
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="stylesheet" type="text/css" href="css/regist.css">
<script src="js/checkfunc.js"></script>
<meta charset="utf-8">
</head>
<body>
<?php include('menu.html');?>
<div id="inputmathcode">
	<span style="font-size: x-large">添削者登録画面</span>
</div>

<form action="exreg_result.php" method="post">

<p>アカウント名：<input type="text" name="account"></p>
<p>パスワード：<input type="password" name="password"></p>
<p>備考：<input type="text" name="bikou"></p>

<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="登録する">

</form>
<hr />
	<div id="mathview">
	登録済み添削者一覧<br>
	<?php
	require_once('userdb.php');
	$pdo = db_connect();
	$sql = "SELECT * FROM examiner ORDER BY id";
	$stmt = $pdo->query($sql);
	?>
	<table id="hv_table">
	<tr>
		<th>id</th>
		<th>ユーザー名</th>
		<th>情報</th>
		<th>削除</th>
	</tr>
	<?php
	foreach ($stmt as $row) {
	?>
		<tr>
			<td><?php echo $row['id']; ?></td>
			<td><?php echo $row['account']; ?></td>
			<td><?php echo $row['data']; ?></td>
			<td>
				<form action="exdelete.php" method="post" onSubmit="return check()">
					<input type="submit" value="削除する">
					<input type="hidden" name="id" value="<?=$row['id']?>">
					<input type="hidden" name="click" value="exdelete">
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