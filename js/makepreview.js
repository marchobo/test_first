//即時関数
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
