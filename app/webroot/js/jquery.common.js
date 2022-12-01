/**
 * jquery共通JavaScript
 *
 * @package		app.views.orders.js
 * @author		N.Kuga
 * @create		2011/08/30
 **/

/**
 * サーバへの問合せ開始時の処理
 */
$(document).ajaxStart(function () {
	$.blockUI({
		timeout: 30000
	});
});

/**
 * サーバへの問合せ終了時の処理
 */
$(document).ajaxStop(function () {
	$.unblockUI();
});

/**
 * ドキュメント読み込み完了時の処理
 */
$(document).ready(function() {
	// ajax問合せをキャッシュしない
	$.ajaxSetup({ cache: false });
});
