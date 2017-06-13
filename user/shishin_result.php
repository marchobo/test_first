<?php
require_once('session_check.php');
//tcpdfとfpdiのインクルード
require_once('../lib/tcpdf/tcpdf.php');
require_once('../lib/fpdi/fpdi.php');

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}
//一旦PDFの準備を行う
//FPDIのインスタンス化
$pdf = new FPDI();
//フォントのインスタンス化
$font = new TCPDF_FONTS();
//余白の設定
$pdf -> SetMargins(0,0,0);
//自動改ページをしない
$pdf -> SetAutoPageBreak(false);
//ヘッダ・フッタを使用しない
$pdf -> setPrintHeader(false);
$pdf -> setPrintFooter(false);
//1ページ目を作成
$pdf -> AddPage();
//フォントの設定
$pdf -> SetFont('times', '', 14);
//既存のPDFをテンプレートとして読み込む
$pdfpath = '../templates/'.$_POST['univcode'].$_POST['shikenshu'].$_POST['nendo'].'.pdf';
$pdf -> setSourceFile($pdfpath);

//既存PDFの1ページ目をテンプレートに設定
$page = $pdf -> importPage(1);
$pdf -> useTemplate($page);
//テキスト色の設定
$pdf -> SetTextColor(220, 20, 60);
//ここまで


//取りこぼし
$koboshis = $_POST['koboshi'];
//合計点算出
$daimons = $_POST['daimon'];
foreach($daimons as $daimontokuten){
	$goukeiten += $daimontokuten;
}

//データベースに情報を登録
//touandata,tensudata,hsdataの順
require_once('../db/sqlconnect.php');
$pdo = db_connect();
try{
	//例外処理を投げる（スロー）ようにする
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//トランザクション開始
	$pdo->beginTransaction();
	//touandataの登録
	$sql = "INSERT INTO touandata (goukeiten) VALUES(?)";
	$st = $pdo -> prepare($sql);
	$st->execute(array($goukeiten));
	//登録したtouanidを採番
	$touanid = $pdo -> lastInsertId();

	//tensudataの登録
	for ($i=0;$i<$_POST['daimonsu'];$i++){
		$sql = "INSERT INTO tensudata VALUES(?, ?, ?)";
		$st = $pdo -> prepare($sql);
		$st->execute(array($touanid, $i+1, $daimons[$i+1]));
	}
	//hsdataの登録
	foreach($koboshis as $key => $value){
		$koboshi = $value;
		$sql = "SELECT * from koumoku WHERE id = ?";
		$st = $pdo -> prepare($sql);
		$st->execute(array($key));
		foreach($st as $row){
			$daimon = $row['daimon'];
			$shomon = $row['shomon'];
			$junban = $row['junban'];
			$haiten = $row['haiten'];
			$rank = $row['rank'];
		}
		//ここでデータ挿入
		$sql = "INSERT INTO hsdata VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())";
		$st = $pdo -> prepare($sql);
		$st->execute(array(0, $touanid, $_SESSION['account'], $_POST['exid'], $_POST['stuid'], $_POST['koza'], $_POST['univcode'], $_POST['shikenshu'], $_POST['nendo'], $_POST['kaisu'], $daimon, $shomon, $junban, $haiten, $rank, $koboshi));
	}

	//既存テンプレートに文字を書き込む
	foreach($koboshis as $key => $value){
		$koboshi = $value;
		$sql = "SELECT * from pdfpos WHERE id = ?";
		$st = $pdo -> prepare($sql);
		$st->execute(array($key));
		foreach($st as $row){
			$posx = $row['posx'];
			$posy = $row['posy'];
		}
		$pdf -> Text($posx,$posy,$koboshi);
	}

	// トランザクション完了（コミット）
	$pdo->commit();
}
catch (PDOException $e){
	//トランザクション取り消し（ロールバック）
	$pdo->rollBack();
	print('Error:'.$e->getMessage());
	echo "<p>データ送信がうまく行われませんでした。もう一度入力からやり直してください。</p>";
	die();
}

//出力前にクリーンにしないとエラー出る
ob_end_clean();

//PDFをブラウザに出力する
$pdf->Output($_POST['pdfname'].'_shishin.pdf', "D");
?>