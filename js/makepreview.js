//即時関数
//id mathcodeに書いたコードを、id previewに表示する関数
$(function() {
	function makePreview() {
		input = $('#mathcode').val().replace(/</g, "&lt;").replace(/>/g, "&gt;");
		$('#preview').html(input);
		MathJax.Hub.Queue(["Typeset",MathJax.Hub,"preview"]);
	}
	$('body').keyup(function(){makePreview()});
	$('body').bind('updated',function(){makePreview()});
	makePreview();
});
