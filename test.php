<?php

<<<<<<< HEAD

=======
//php情報読み込み
//phpinfo();
//クリーン
//ob_end_clean();
>>>>>>> 一時的
//tcpdfとfpdiのインクルード
require_once('lib/tcpdf/tcpdf.php');
require_once('lib/fpdi/fpdi.php');

//mysql呼んでみる

$my_Con = mysqli_connect("localhost","root","t873n338");
if ($my_Con == false){
	die("MYSQLの接続に失敗しました。");
}
{echo"接続成功！";}

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
$pdf -> setSourceFile('S16.pdf');
//既存PDFの1ページ目をテンプレートに設定
$page = $pdf -> importPage(1);
$pdf -> useTemplate($page);
//テキスト色の設定
$pdf -> SetTextColor(220, 20, 60);
//既存テンプレートに文字を書き込む
$pdf -> Text(195,88,"5");
$pdf -> Text(195,94,"10");

/*
TCPDFで新規作成する場合
$pdf = new TCPDF("L", "mm", "A4", true, "UTF-8" );
*/


//出力前にクリーンにしないとエラー出る
ob_end_clean();
//PDFをブラウザに出力する
$pdf->Output("test.pdf", "I");
?>
