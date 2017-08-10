<?php
	// file name: call_python.php
	$fullPath = 'python python/source2.py';
	exec($fullPath, $outpara);
	echo '<HTML>';
	echo '<head>';
	echo '<title>Pythonのテスト</title>';
	echo '</head>';
	echo '<body>';
	echo '<PRE>';
	var_dump($fullPath);
	var_dump($outpara[0]);
	echo '<PRE>';
	echo '</body>';
	echo '</HTML>';
	// ファイルのパス
	$filepath = 'pdf/mergedfile.pdf';
	// リネーム後のファイル名
	$filename = 'download.pdf';

	// ファイルタイプにPDFを指定
	header('Content-Type: application/pdf');

	// ファイルサイズを取得し、ダウンロードの進捗を表示
	header('Content-Length: '.filesize($filepath));

	// ファイルのダウンロード、リネームを指示
	header('Content-Disposition: attachment; filename="'.$filename.'"');

	ob_end_clean();

	// ファイルを読み込みダウンロードを実行
	readfile($filepath);

	exit;
?>