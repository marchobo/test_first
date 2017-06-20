<?php
require_once('session_check.php');

//tcpdfとfpdiのインクルード
require_once('lib/tcpdf/tcpdf.php');
require_once('lib/fpdi/fpdi.php');

//SQLに接続
require_once('db/sqlconnect.php');
$pdo = db_connect();
$sql = "select * from pdf where id = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($_SESSION['pdfid']));
foreach($st as $row){
	$univcode=$row['univcode'];
	$shikenshu=$row['shikenshu'];
	$nendo=$row['nendo'];
}

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
$pdfpath = 'templates/'.$univcode.$shikenshu.$nendo.'.pdf';
$pdf -> setSourceFile($pdfpath);

//既存PDFの1ページ目をテンプレートに設定
$page = $pdf -> importPage(1);
$pdf -> useTemplate($page);
//テキスト色の設定
$pdf -> SetTextColor(220, 20, 60);

//既存テンプレートに文字を書き込む
$sql = "select * from pdfpos where pdfid = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($_SESSION['pdfid']));
foreach($st as $row){
	$pdf -> Text($row['posx'],$row['posy'],"00");
}

//その他項目について、文字を書き込む
//テキスト色の設定
$pdf -> SetTextColor(10, 10, 220);
$sql = "select * from pdfpos_sub where pdfid = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($_SESSION['pdfid']));
foreach($st as $row){
	//フォントの設定
	$sql = "select * from subitem where id = ?";
	$st2 = $pdo -> prepare($sql);
	$st2->execute(array($row['itemid']));
	foreach($st2 as $row2){
		$size=$row2['size'];
	}
	$pdf -> SetFont('kozgopromedium', '', $size);
	$pdf -> Text($row['posx'],$row['posy'],"00");
}

//imgについて、大きさに応じた矩形を書き込む
$sql = "select * from pdfpos_img where pdfid = ?";
$st = $pdo -> prepare($sql);
$st->execute(array($_SESSION['pdfid']));
foreach($st as $row){
	//描画方法の設定
	$sql = "select * from imgitem where id = ?";
	$st2 = $pdo -> prepare($sql);
	$st2->execute(array($row['imgid']));
	foreach($st2 as $row2){
		$width=$row2['width'];
		$height=$row2['height'];
	}
	//imgの枠線の色と太さを決定
	$pdf->SetDrawColor(220, 20, 60);
	$pdf->SetLineWidth(0.5);
	$pdf -> Rect($row['posx'],$row['posy'], $width, $height, 'D', null, array(220, 20, 60));
}

//出力前にクリーンにしないとエラー出る
ob_end_clean();
//セッションを設定
$_SESSION['click']='posreg';

//PDFをブラウザに出力する
$pdf->Output('preview_'.$univcode.$shikenshu.$nendo.'.pdf', "D");
?>
