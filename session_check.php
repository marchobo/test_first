<?php
session_start();

// ログイン状態のチェック
if (!isset($_SESSION["account"])) {
	header("Location: login_form.php");
	exit();
}
?>