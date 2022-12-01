/**
 * 元ウィンドウに値を渡して、ウィンドウを閉じる
 */
function closeWindow(id, name) {
	// ブラウザの種類を判定してコードを切り分ける
	if (navigator.userAgent.indexOf("MSIE") != -1) {
		var parentWindow = window.dialogArguments;
		parentWindow.setAlmaMater(id, name);
	} else {
		opener.setAlmaMater(id, name);
	}
	
	window.close();
}
