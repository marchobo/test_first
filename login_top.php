<?php
require_once('session_check.php');
//regist画面はunivcodeなどがないと動かないので、セッションで管理
$_SESSION['univcode']='';
$_SESSION['shikenshu']='';
$_SESSION['nendo']='';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" type="text/css" href="css/regist.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">

<meta charset="UTF-8">
<title>TOP</title>
</head>
<body>
	<?php include('menu.html');?>
	<div id="inputmathcode">
		<span style="font-size: x-large">復習の指針管理システムへようこそ！</span>
	</div>
</body>
</html>

