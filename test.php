<?php
//tcpdfとfpdiのインクルード
require_once('lib/tcpdf/tcpdf.php');
require_once('lib/fpdi/fpdi.php');

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
$pdfpath = 'templates/1408302007.pdf';
$pdf -> setSourceFile($pdfpath);

//既存PDFの1ページ目をテンプレートに設定
$page = $pdf -> importPage(1);
$pdf -> useTemplate($page);
//テキスト色の設定
$pdf -> SetTextColor(220, 20, 60);
//ここまで

//pdfに貼り付け
$path = 'templates/1408302007/0.jpg';

$pdf -> Image($path, 100,100,80, 60);

$pdf -> Text(100,100,'00');

//出力前にクリーンにしないとエラー出る
ob_end_clean();

//PDFをブラウザに出力する
$pdf->Output('1408302007_shishin.pdf', "I");
?>