<?php
/* pChart library inclusions */
include("lib/pChart2.1.4/class/pData.class.php");
include("lib/pChart2.1.4/class/pDraw.class.php");
include("lib/pChart2.1.4/class/pImage.class.php");

// データセット用オブジェクトの生成
$myData = new pData();

// データセット1を追加
$myData->addPoints(array(7,2,6,27,47,3,26,28),"Serie1");
// データセット1のラベル
$myData->setSerieDescription("Serie1","あなたの位置");
$myData->setSerieOnAxis("Serie1",0);

// X軸に表示する項目
$myData->addPoints(array("1月","2月","3月","4月","5月","6月","7月","8月"),"Absissa");
$myData->setAbscissa("Absissa");

// グラフのサイズとデータセットを引数に渡してpChartオブジェクトを生成
$myPicture = new pImage(1080,810,$myData);

// フォントとサイズを指定
$myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>32));

// グラフの出力位置と大きさを指定(右からX軸、Y軸、幅、高さ)
$myPicture->setGraphArea(120,120,950,750);

// 背景色を指定
$Settings = array("R"=>210, "G"=>210, "B"=>210);
// 背景を描く
$myPicture->drawFilledRectangle(0,0,1080,810,$Settings);

// テキストの出力位置と文字の色を指定
$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE, "R"=>0, "G"=>0, "B"=>0);
// テキストの出力位置、文字列とフォントの情報を引数にしてタイトルを出力
$myPicture->drawText(500,50,"販売実績(%)",$TextSettings);

// フォントサイズを切り替え
$myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>16));

// メモリを描く
$myPicture->drawScale();

//一旦、生徒の位置の色にパレット設定して凡例も書く
$myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>30));
$myData->setPalette("Serie1",array("R"=>0,"G"=>0,"B"=>255));
$myPicture->drawLegend(20,20,array("BoxWidth"=>20,"BoxHeight"=>20,"Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

// フォントサイズを切り替え
$myPicture->setFontProperties(array("FontName"=>"lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>16));

/* Create the per bar palette */
$Palette = array("0"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255),
		"1"=>array("R"=>0,"G"=>0,"B"=>255,"Alpha"=>255),
		"2"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255),
		"3"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255),
		"4"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255),
		"5"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255),
		"6"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255),
		"7"=>array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255));



// グラフのバーにデータセットの数値を表示する、グラデーションを付けるオプション
$Config = array("DisplayValues"=>1, "OverrideColors"=>$Palette);
// 描くグラフの種類をメソッド名で指定
$myPicture->drawBarChart($Config);

// 描いたグラフの保存
$myPicture->render("mypic.png");
//$myPicture->autoOutput("mypic.png");



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
$pdf -> SetFont('kacho', '', 14);
//既存のPDFをテンプレートとして読み込む
$pdfpath = 'templates/1408302007.pdf';
$pdf -> setSourceFile($pdfpath);

//既存PDFの1ページ目をテンプレートに設定
$page = $pdf -> importPage(1);
$pdf -> useTemplate($page);
//テキスト色の設定
$pdf -> SetTextColor(220, 20, 60);
//ここまで



$pdf -> Image('mypic.png', 100,100,108,81);
$pdf->SetLineWidth(0.3);


//出力前にクリーンにしないとエラー出る
ob_end_clean();

//PDFをブラウザに出力する
$pdf->Output('test_shishin.pdf', "D");
?>