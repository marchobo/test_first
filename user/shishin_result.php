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
/* pChart のライブラリを読み込む */
include("../lib/pChart2.1.4/class/pData.class.php");
include("../lib/pChart2.1.4/class/pDraw.class.php");
include("../lib/pChart2.1.4/class/pImage.class.php");

//FPDIのインスタンス化
$pdf = new FPDI();
//フォントのインスタンス化
$font = new TCPDF_FONTS();
//花鳥風月の設定(初回のみ)
//$font_path1 = '../lib/tcpdf/fonts/kacho/kacho-regular.ttf';
//$font_path2 = '../lib/tcpdf/fonts/kacho/kacho-bold.ttf';
//$kachor = $font->addTTFfont($font_path1);
//$kachob = $font->addTTFfont($font_path2);
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
$goukeiten = 0;
foreach($daimons as $daimontokuten){
	$goukeiten += $daimontokuten;
}
//取りこぼし合計点などの定義
$items['xall']=0;
$items['aall']=0;
$items['ball']=0;
$items['xpoints']=0;
$items['xapoints']=0;
$items['xabpoints']=0;
$devvalue=0;
$imgs['histo'] = 0;

//範囲外得点
$hanigai=$_POST['hanigai'];

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
			switch ($rank){
				case 0:
					$items['xall']+=$koboshi;
					break;
				case 1:
					$items['aall']+=$koboshi;
					break;
				case 2:
					$items['ball']+=$koboshi;
					break;
			}
		}
		//ここでデータ挿入
		$pdo->query("set time_zone = '+09:00'");
		$st = $pdo->query("SELECT now()");//現在時刻を求めておく
		foreach($st as $key => $value){
			$now = $value[0];
		}
		$sql = "INSERT INTO hsdata VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$st = $pdo -> prepare($sql);
		$st->execute(array(0, $touanid, $_SESSION['account_ex'], $_POST['exid'], $_POST['stuid'], $_POST['koza'], $_POST['univcode'], $_POST['shikenshu'], $_POST['nendo'], $_POST['kaisu'], $daimon, $shomon, $junban, $haiten, $rank, $koboshi, $now));
	}
	//「～が取れれば」を求める
	$items['xpoints']= $goukeiten - $hanigai + $items['xall'];
	$items['xapoints']= $items['xpoints']+ $items['aall'];
	$items['xabpoints']= $items['xapoints']+ $items['ball'];

	//偏差値を求める
	$sql = "SELECT * from devvaldata WHERE univcode = ? and shikenshu = ? and nendo = ? and point = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($_POST['univcode'], $_POST['shikenshu'], $_POST['nendo'], $goukeiten));
	foreach($st as $row){
		$devvalue=round($row['devval'],1);
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
			$pdfid = $row['pdfid'];
		}
		$pdf -> Text($posx,$posy,$koboshi);
	}

	//ヒストグラムを追加する
	//階級値を求める
	$classval = ceil($goukeiten/10);
	//答案の満点を求める
	$manten=0;
	$sql = "SELECT * from shikendata WHERE pdfid = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($pdfid));
	foreach($st as $row){
		$manten+=$row['manten'];
	}
	//得点の状態stageを求める（赤黄緑青）
	$stage=0;
	$w = $goukeiten/$manten;
	if($w<=0.25){
		$stage=1;
	}elseif($w<=0.5){
		$stage=2;
	}elseif($w<=0.75){
		$stage=3;
	}else{
		$stage=4;
	}
	//ここからヒストグラムを作成
	// データセット用オブジェクトの生成
	$myData = new pData();
	// ヒストグラムのデータセットを追加
	$histoarray = array();
	$xarray = array();
	$sql = "SELECT * from histodata WHERE univcode = ? and shikenshu = ? and nendo = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($_POST['univcode'], $_POST['shikenshu'], $_POST['nendo']));
	foreach($st as $row){
		$histoarray[]=$row['wariai'];
		if($row['point']>0){
			$xarray[]='～'.$row['point'];
		}else{
			$xarray[]=$row['point'];
		}
	}
	$myData->addPoints($histoarray,"histo");
	// ヒストグラムのラベル
	$myData->setSerieDescription("histo"," あなたの位置");
	$myData->setSerieOnAxis("histo",0);

	// X軸に表示する項目
	$myData->addPoints($xarray,"Absissa");
	$myData->setAbscissa("Absissa");

	// グラフのサイズとデータセットを引数に渡してpChartオブジェクトを生成
	$myPicture = new pImage(1200,900,$myData);

	// フォントとサイズを指定
	$myPicture->setFontProperties(array("FontName"=>"../lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>40));

	// グラフの出力位置と大きさを指定(右からX軸、Y軸、幅、高さ)
	$myPicture->setGraphArea(75,150,1125,850);

	// 背景色を指定
	$Settings = array("R"=>235, "G"=>235, "B"=>235);
	// 背景を描く
	$myPicture->drawFilledRectangle(0,0,1200,900,$Settings);

	/*タイトル*/
	// フォントとサイズを指定
	$myPicture->setFontProperties(array("FontName"=>"../lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>40));
	// タイトルの出力位置と文字の色を指定
	$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE, "R"=>0, "G"=>0, "B"=>0);
	// テキストの出力位置、文字列とフォントの情報を引数にしてタイトルを出力
	$myPicture->drawText(600,50,"得点分布(%)",$TextSettings);

	/*点数情報*/
	// フォントとサイズを指定
	$myPicture->setFontProperties(array("FontName"=>"../lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>30));
	// 合計点の出力位置と文字の色を指定
	$TextSettings = array("Align"=>TEXT_ALIGN_TOPLEFT, "R"=>0, "G"=>0, "B"=>0);
	// 合計点の出力位置、文字列とフォントの情報を引数にしてタイトルを出力
	$myPicture->drawText(850,170,"得点： ".$goukeiten." / ".$manten,$TextSettings);

	// 偏差値の出力位置と文字の色を指定
	$TextSettings = array("Align"=>TEXT_ALIGN_TOPLEFT, "R"=>0, "G"=>0, "B"=>0);
	// 偏差値の出力位置、文字列とフォントの情報を引数にしてタイトルを出力
	$myPicture->drawText(850,220,"偏差値： ".$devvalue,$TextSettings);



	// フォントサイズを切り替え
	$myPicture->setFontProperties(array("FontName"=>"../lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>22));

	// 目盛を書く
	$myPicture->drawScale();

	//一旦、生徒の位置の色にパレット設定して凡例も書く
	//場合分け
	$legcolarray = array();//凡例用
	$barcolarray = array();//生徒の位置の棒グラフ用
	$grayarray = array("R"=>100,"G"=>100,"B"=>100,"Alpha"=>255);//生徒の位置以外の棒グラフ用
	switch($stage){
		case 1:
			$legcolarray = array("R"=>255,"G"=>0,"B"=>0);
			$barcolarray = array("R"=>255,"G"=>0,"B"=>0,"Alpha"=>255);
			break;
		case 2:
			$legcolarray = array("R"=>255,"G"=>255,"B"=>0);
			$barcolarray = array("R"=>255,"G"=>255,"B"=>0,"Alpha"=>255);
			break;
		case 3:
			$legcolarray = array("R"=>0,"G"=>255,"B"=>0);
			$barcolarray = array("R"=>0,"G"=>255,"B"=>0,"Alpha"=>255);
			break;
		case 4:
			$legcolarray = array("R"=>0,"G"=>0,"B"=>255);
			$barcolarray = array("R"=>0,"G"=>0,"B"=>255,"Alpha"=>255);
			break;
		default:
			die();
	}
	//実際に書く
	$myPicture->setFontProperties(array("FontName"=>"../lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>32));
	$myData->setPalette("histo",$legcolarray);
	$myPicture->drawLegend(850,50,array("BoxWidth"=>30,"BoxHeight"=>30,"Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

	// フォントサイズを切り替え
	$myPicture->setFontProperties(array("FontName"=>"../lib/pChart2.1.4/fonts/kacho-regular.ttf","FontSize"=>22));

	/* 棒グラフのパレットの設定 */
	$Palette = array();
	$sql = "SELECT * from histodata WHERE univcode = ? and shikenshu = ? and nendo = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($_POST['univcode'], $_POST['shikenshu'], $_POST['nendo']));
	foreach($st as $row){
		$point_order = $row['point']/10;
		if($point_order==$classval){
			$Palette = $Palette + array($point_order => $barcolarray);
		}else{
			$Palette = $Palette + array($point_order => $grayarray);
		}
	}

	// グラフのバーにデータセットの数値を表示する、グラデーションを付けるオプション
	$Config = array("DisplayValues"=>1, "OverrideColors"=>$Palette);

	/* Enable shadow support */
	$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
	// 描くグラフの種類をメソッド名で指定
	$myPicture->drawBarChart($Config);

	// 描いたグラフの保存
	$myPicture->render("histo.png");

	//pdfに貼り付け
	$paths['histo'] = 'histo.png';
	$sql = "SELECT * from pdfpos_img WHERE pdfid = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($pdfid));
	foreach($st as $row){
		$imgid = $row['imgid'];
		$posx = $row['posx'];
		$posy = $row['posy'];
		//項目の名称を求める
		$sql2 = "SELECT * from imgitem WHERE id = ?";
		$st2 = $pdo -> prepare($sql2);
		$st2->execute(array($imgid));
		foreach($st2 as $row2){
			$name = $row2['name'];
			$width = $row2['width'];
			$height = $row2['height'];
		}
		//名称とimgsのキーが一致する場合に印字する
		foreach($imgs as $key => $value){
			if($key == $name){
				$pdf -> Image($paths[$name], $posx,$posy,$width,$height);
				$pdf->SetLineWidth(0.3);
				//$pdf->SetDrawColor(220, 20, 60);
				//$pdf -> Rect($posx,$posy,$width,$height, 'D');
			}
		}
	}
	//テキスト色の設定
	$pdf -> SetTextColor(220, 20, 60);
	//既存テンプレートに取りこぼし合計点等を書き込む
	$name=null;
	$size=null;
	$sql = "SELECT * from pdfpos_sub WHERE pdfid = ?";
	$st = $pdo -> prepare($sql);
	$st->execute(array($pdfid));
	foreach($st as $row){
		$itemid = $row['itemid'];
		$posx = $row['posx'];
		$posy = $row['posy'];
		//項目の名称を求める
		$sql2 = "SELECT * from subitem WHERE id = ?";
		$st2 = $pdo -> prepare($sql2);
		$st2->execute(array($itemid));
		foreach($st2 as $row2){
			$name = $row2['name'];
			$size = $row2['size'];
		}
		//名称とitemsのキーが一致する場合に印字する
		foreach($items as $key => $value){
			if($key == $name){
				$pdf -> SetFont('kachob', '', $size);

				$pdf -> Text($posx,$posy,$value);
			}
		}
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