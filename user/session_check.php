<?php
session_start();

// ログイン状態のチェック
if (!isset($_SESSION["account_ex"])) {
	header("Location: login.php");
	exit();
}
?>