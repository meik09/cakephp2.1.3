// アプリケーションURL
var appUrl = '';

/**
 * 値の検索画面を表示
 */
function openWindow(controller, action) {
	url = appUrl + "/" + controller + "/" + action + "/1";
	win = window.showModalDialog(url, this, "dialogWidth=516px;dialogHeight=582px;resizable=no;maximize=no;location=no;scroll=no;status=no;");
}
