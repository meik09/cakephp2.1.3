/**
 * ドキュメント読み込み完了時の処理
 */
$(document).ready(function() {
    // ajax問合せをキャッシュしない
    $.ajaxSetup({ cache: false });
});

/**
 * ユーザ削除時の確認
 */
function onSubmit4Delete() {
	var submitFlg = false;
	if (confirm('表示されている在籍者を削除してもよろしいでしょうか？')) submitFlg = true;
	return submitFlg;
}
